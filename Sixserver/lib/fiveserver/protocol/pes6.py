"""
Protocol implementations for PES6
"""

from twisted.internet import reactor, defer
from twisted.application import service
from twisted.web import client

from Crypto.Cipher import Blowfish
from datetime import datetime, timedelta, time
from hashlib import md5
import binascii
import struct
import time
import re
import zlib
import sys
import traceback
from fiveserver.model import packet, user, lobby, util
from fiveserver.model.util import PacketFormatter
from fiveserver.model.lobby import MatchState
from fiveserver import log, stream, errors
from fiveserver.protocol import PacketDispatcher, isSameGame
from fiveserver.protocol import pes5


CHAT_HISTORY_DELAY = 3  # seconds

ERRORS = [
    '\xff\xff\xfd\xb6', # owner cancelled
    '\xff\xff\xfd\xbb', # only 4 players can participate
    '\xff\xff\xfe\x00', # deadline passed
]

def getHomePlayerNames(match):
    home_players = [match.teamSelection.home_captain]
    home_players.extend(match.teamSelection.home_more_players)
    names = ""
    cnt = len(home_players)
    # log.msg('getHomePlayerNames: len(home_players)=%d' % cnt)
    if (cnt > 0):
      try:
        names = ','.join([x.name for x in home_players])
        names = names.encode('ascii','ignore')
      except:
        log.msg('ERROR in getHomePlayerNames')
        exc_type, exc_value, exc_traceback = sys.exc_info()
        lines = traceback.format_exception(exc_type, exc_value, exc_traceback)
        log.msg("Lines: %s" % lines)
    return names

def getAwayPlayerNames(match):
    away_players = [match.teamSelection.away_captain]
    away_players.extend(match.teamSelection.away_more_players)
    names = ""
    cnt = len(away_players)
    # log.msg('getAwayPlayerNames: len(away_players)=%d' % cnt)
    if (cnt > 0):
      try:
        names = ','.join([x.name for x in away_players])
        names = names.encode('ascii','ignore')
      except:
        log.msg('ERROR in getAwayPlayerNames')
        exc_type, exc_value, exc_traceback = sys.exc_info()
        lines = traceback.format_exception(exc_type, exc_value, exc_traceback)
        log.msg("Lines: %s" % lines)
    return names

def getHomePlayerIdsMore(match):
    home_players = []
    home_players.extend(match.teamSelection.home_more_players)
    profileHome2 = 0
    profileHome3 = 0
    if (len(home_players) > 0):
      profileHome2 = home_players[0].id
      if (len(home_players) > 1):
        profileHome3 = home_players[1].id
    return profileHome2, profileHome3

def getAwayPlayerIdsMore(match):
    away_players = []
    away_players.extend(match.teamSelection.away_more_players)
    profileAway2 = 0
    profileAway3 = 0
    if (len(away_players) > 0):
      profileAway2 = away_players[0].id
      if (len(away_players) > 1):
        profileAway3 = away_players[1].id
    return profileAway2, profileAway3

class NewsProtocol(pes5.NewsProtocol):
    """
    News-service for PES6
    """

    GREETING = {
        'title': 'SYSTEM: Sixserver v%s',
        'text': ('Welcome to Sixserver.\r\n'
                 '\r\n'
                 'Have a good time, play fair, and please\r\n'
                 'support us by donating.\r\n'
                 '\r\n'
                 'evo-league Sixserver is based on Fiveserver\r\n'
                 'originally created by reddwarf and juce.\r\n')
    }

    SERVER_NAME = 'evo-league Sixserver'

    NEW_FEATURES = {
        '6.0': (
            'evo-league top donators 2014',
            'PES6 Online Viet Nam\r\n'
            'AIORhythm group\r\n'
            'TR_PES6ciyiz.Biz\r\n'
            'Friendo\r\n'
            'Adrenaline\r\n'
            'evolegend\r\n'
            'HENKELARSSON\r\n'
            'unaffected\r\n'
            'atlasV\r\n'
            'PESKING\r\n'
            '\r\n'
            'Please support us! http://tiny.cc/saveevo'
            )
    }

    def register(self):
        pes5.NewsProtocol.register(self)
        self.addHandler(0x2200, self.getWebServerList_2200)

    def getServerList_2005(self, pkt):
        log.debug('pes6.getServerList_2005')
        myport = self.transport.getHost().port
        gameName = None
        for name,port in self.factory.serverConfig.GamePorts.items():
            if port == myport:
                gameName = name
                break
        serverIP = self.factory.configuration.serverIP_wan
        if serverIP is None:
            serverIP = "91.250.116.226"
            log.msg('serverIP was None, set to %s' % serverIP)
        servers = [
            (-1,2,'LOGIN',serverIP,
             self.factory.serverConfig.NetworkServer['loginService'][gameName],
             0,2),
            (-1,3,self.SERVER_NAME.encode('utf-8'),serverIP,
             self.factory.serverConfig.NetworkServer['mainService'],
             max(0, self.factory.getNumUsersOnline()-1),3),
            (-1,8,'NETWORK_MENU',serverIP,
             self.factory.serverConfig.NetworkServer['networkMenuService'],
             0,8),
        ]

        data = ''.join(['%s%s%s%s%s%s%s' % (
                struct.pack('!i',a),
                struct.pack('!i',b),
                '%s%s' % (name,'\0'*(32-len(name[:32]))),
                '%s%s' % (ip,'\0'*(15-len(ip))),
                struct.pack('!H',port),
                struct.pack('!H',c),
                struct.pack('!H',d)) for a,b,name,ip,port,c,d in servers])
        self.sendZeros(0x2002,4)
        self.sendData(0x2003,data)
        self.sendZeros(0x2004,4)

    def getWebServerList_2200(self, pkt):
        log.debug('pes6.getWebServerList_2200')
        self.sendZeros(0x2201,4)
        #self.sendData(0x2202,data) #TODO
        self.sendZeros(0x2203,4)


class RosterHandler:
    """
    Provide means of extracting roster hash
    from the client auth packet data.
    """

    def getRosterHash(self, pkt_data):
        return pkt_data[58:74]


class LoginService(RosterHandler, pes5.LoginService):
    """
    Login-service for PES6
    """

    @defer.inlineCallbacks
    def getProfiles_3010(self, pkt):
        log.debug('pes6.getProfiles_3010')
        if self.factory.serverConfig.ShowStats:
            results = yield defer.DeferredList([
                self.factory.matchData.getGames(
                    profile.id) for profile in self._user.profiles])
            profiles = self._user.profiles
        else:
            # hide all stats
            results = yield defer.succeed([(True, 0)
                for profile in self._user.profiles])
            profiles = [self.makePristineProfile(profile)
                for profile in self._user.profiles]
        data = '\0'*4 + ''.join([
            '%(index)s%(id)s%(name)s%(playTime)s'
            '%(division)s%(points)s%(rating)s%(games)s' % {
                'index':struct.pack('!B', i),
                'id':struct.pack('!i', profile.id),
                'name':util.padWithZeros(profile.name, 48),
                'division':struct.pack('!B', 
                    self.factory.ratingMath.getDivision(profile.rating)),
                'playTime':struct.pack('!i', profile.playTime.seconds),
                'points':struct.pack('!i', profile.points),
                'games':struct.pack('!H', games),
                'rating':struct.pack('!H',profile.rating),
                } 
            for (_, games), (i, profile) in zip(
                results, enumerate(profiles))])
        self.sendData(0x3012, data)
        defer.returnValue(None)

    def getMatchResults_3070(self, pkt):
        log.debug('pes6.getMatchResults_3070')
        self.sendZeros(0x3071,4)
        self.sendZeros(0x3073,4)

    def do_3120(self, pkt):
        log.debug('pes6.do_3120')
        self.sendZeros(0x3121,4)
        self.sendZeros(0x3123,0)


class LoginServicePES6(LoginService):
    """
    Specific implementation of login service for PES6
    """

    def __init__(self):
        LoginService.__init__(self)
        self.gameName = 'pes6'


class LoginServiceWE2007(LoginService):
    """
    Specific implementation of login service for WE2007
    """

    def __init__(self):
        LoginService.__init__(self)
        self.gameName = 'we2007'


class NetworkMenuService(RosterHandler, pes5.NetworkMenuService):
    """
    PES6 implementation.
    The service that communicates with the player, when
    he/she is in the "NETWORK MENU" mode.
    """

class MainService(RosterHandler, pes5.MainService):
    """
    PES6 implementation
    The main game server, which keeps track of matches, goals
    and other important statistics.
    """

    @defer.inlineCallbacks
    def connectionLost(self, reason):
        log.debug('pes6.connectionLost')
        pes5.LoginService.connectionLost(self, reason)
        try:
            if self._user:
                if self._user.state:
                    log.debug('connectionLost: self._user.state=%s' % self._user.state)
                    room = self._user.state.room
                    if room:
                        log.debug('connectionLost: room.name=%s' % room.name)
                        room.cancelParticipation(self._user)
                        yield self.exitingRoom(room, self._user)
                        # update participation of remaining players in room
                        data = self.formatRoomParticipationStatus(room)
                        for player in room.players:                
                            player.sendData(0x4365, data)
                    self.exitingLobby(self._user)
                try:
                    log.debug('connectionLost: profiles=%s' % ','.join([x.name for x in self._user.profiles]))
                    self.factory.statsData.WriteAccessLogEntry(self._user.username, self.addr.host, '6Q')
                    log.msg('pes6.connectionLost: Write access log: type=6Q user=%s profiles=%s ip=%s' % (self._user.username, ','.join([x.name for x in self._user.profiles]), self.addr.host))
                except:
                    log.msg("pes6.connectionLost: Write access log: %s" % sys.exc_info()[0])
                    exc_type, exc_value, exc_traceback = sys.exc_info()
                    lines = traceback.format_exception(exc_type, exc_value, exc_traceback)
                    log.msg("Lines: %s" % lines)
            else:
                log.msg('pes6.connectionLost: self._user is None')
        except:
            log.msg('ERROR in connectionLost')
            exc_type, exc_value, exc_traceback = sys.exc_info()
            lines = traceback.format_exception(exc_type, exc_value, exc_traceback)
            log.msg("Lines: %s" % lines)

    def formatPlayerInfo(self, usr, roomId, stats=None):
        if stats is None:
            stats = user.Stats(usr.profile.id, 0,0,0,0,0,0,0,0,0,0,0)
        return ('%(id)s%(name)s%(groupid)s%(groupname)s'
                '%(groupmemberstatus)s%(division)s%(roomid)s'
                '%(points)s%(rating)s%(matches)s%(wins)s'
                '%(losses)s%(draws)s%(pad1)s' % {
            'id': struct.pack('!i',usr.profile.id),
            'name': util.padWithZeros(usr.profile.name,48),
            'groupid': struct.pack('!i',0),
            'groupname': util.padWithZeros(usr.profile.groupName,48),
            'groupmemberstatus': struct.pack('!B',0),
            'division': struct.pack('!B', 
                self.factory.ratingMath.getDivision(usr.profile.rating)),
            'roomid': struct.pack('!i',roomId),
            'points': struct.pack('!i',usr.profile.points),
            'rating': struct.pack('!H',usr.profile.rating),
            'matches': struct.pack('!H',
                stats.wins + stats.losses + stats.draws),
            'wins': struct.pack('!H',stats.wins),
            'losses': struct.pack('!H',stats.losses),
            'draws': struct.pack('!H',stats.draws),
            'pad1': '\0'*3,
        })

    def formatProfileInfo(self, profile, stats):
        if not self.factory.serverConfig.ShowStats:
            profile = self.makePristineProfile(profile)
        
        recentTeams = '\xff\xff'*5
        try:
            recentTeams = ''.join([struct.pack('!H', team) for team in stats.teams]) + '\xff\xff'*(5-len(stats.teams))
        except:
            log.msg('pes6.formatProfileInfo: could not get recent teams')
            
        return ('%(id)s%(name)s%(groupid)s%(groupname)s'
                    '%(groupmemberstatus)s%(division)s'
                    '%(points)s%(rating)s%(matches)s'
                    '%(wins)s%(losses)s%(draws)s%(win-strk)s'
                    '%(win-best)s%(disconnects)s'
                    '%(goals-scored)s%(goals-allowed)s'
                    '%(comment)s%(rank)s'
                    '%(competition-gold-medals)s%(competition-silver-medals)s'
                    '%(unknown1)s'
                    '%(winnerscup-gold-medals)s%(winnerscup-silver-medals)s'
                    '%(unknown2)s%(unknown3)s'
                    '%(language)s%(recent-used-teams)s' % {
                'id': struct.pack('!i',profile.id),
                'name': util.padWithZeros(profile.name,48),
                'groupid': struct.pack('!i',0),
                'groupname': util.padWithZeros(profile.groupName,48),
                'groupmemberstatus': struct.pack('!B',1),
                'division': struct.pack('!B', 
                    self.factory.ratingMath.getDivision(profile.rating)),
                'points': struct.pack('!i',profile.points),
                'rating': struct.pack('!H',profile.rating),
                'matches': struct.pack('!H',
                    stats.wins + stats.losses + stats.draws),
                'wins': struct.pack('!H',stats.wins),
                'losses': struct.pack('!H',stats.losses),
                'draws': struct.pack('!H',stats.draws),
                'win-strk': struct.pack('!H', stats.streak_current),
                'win-best': struct.pack('!H', stats.streak_best),
                'disconnects': struct.pack(
                    '!H', profile.disconnects),
                'goals-scored': struct.pack('!i', stats.goals_scored),
                'goals-allowed': struct.pack('!i', stats.goals_allowed),
                'comment': util.padWithZeros((
                    profile.comment or 'evo-league rules!'), 256),
                'rank': struct.pack('!i',profile.rank),
                'competition-gold-medals': struct.pack('!H', 0),
                'competition-silver-medals': struct.pack('!H', 0),
                'unknown1': struct.pack('!H', 0),
                'winnerscup-gold-medals': struct.pack('!H', 0),
                'winnerscup-silver-medals': struct.pack('!H', 0),
                'unknown2': struct.pack('!H', 0),
                'unknown3': struct.pack('!B', 0),
                'language': struct.pack('!B', 0),
                'recent-used-teams': recentTeams 
            })
            
    def formatHomeOrAway(self, room, usr):
        if room.teamSelection:
            return room.teamSelection.getHomeOrAway(usr)
        return 0xff

    def formatTeamsAndGoals(self, room):
        homeTeam, awayTeam = 0xffff, 0xffff
        if room.teamSelection:
            homeTeam = (room.teamSelection.home_team_id
            if room.teamSelection.home_team_id != None else 0xffff)
            awayTeam = (room.teamSelection.away_team_id
            if room.teamSelection.away_team_id != None else 0xffff)
        (homeGoals1st, homeGoals2nd, homeGoalsEt1, 
         homeGoalsEt2, homeGoalsPen) = 0, 0, 0, 0, 0
        (awayGoals1st, awayGoals2nd, awayGoalsEt1, 
         awayGoalsEt2, awayGoalsPen) = 0, 0, 0, 0, 0
        if room.match:
            homeGoals1st = room.match.score_home_1st
            homeGoals2nd = room.match.score_home_2nd
            homeGoalsEt1 = room.match.score_home_et1
            homeGoalsEt2 = room.match.score_home_et2
            homeGoalsPen = room.match.score_home_pen
            awayGoals1st = room.match.score_away_1st
            awayGoals2nd = room.match.score_away_2nd
            awayGoalsEt1 = room.match.score_away_et1
            awayGoalsEt2 = room.match.score_away_et2
            awayGoalsPen = room.match.score_away_pen
        return '%s%s%s%s%s%s%s%s%s%s%s%s' % (
            struct.pack('!H', homeTeam),
            struct.pack('!B', homeGoals1st), # 1st
            struct.pack('!B', homeGoals2nd), # 2nd
            struct.pack('!B', homeGoalsEt1), # et1
            struct.pack('!B', homeGoalsEt2), # et2
            struct.pack('!B', homeGoalsPen), # pen
            struct.pack('!H', awayTeam),
            struct.pack('!B', awayGoals1st), # 1st
            struct.pack('!B', awayGoals2nd), # 2nd
            struct.pack('!B', awayGoalsEt1), # et1
            struct.pack('!B', awayGoalsEt2), # et2
            struct.pack('!B', awayGoalsPen)) # pen

    def formatRoomInfo(self, room):
        n = len(room.players)
        #try:
            # log.msg('formatRoomInfo: len(room.players)=%s' % n)
        #except:
        #    log.msg('ERROR in formatRoomInfo')
        #    exc_type, exc_value, exc_traceback = sys.exc_info()
        #    lines = traceback.format_exception(exc_type, exc_value, exc_traceback)
        #    log.msg("Lines: %s" % lines)
        
        if room.match:
            match_state = room.match.state
            match_clock = room.match.clock
        else:
            match_state, match_clock = 0, 0
        return '%s%s%s%s%s%s%s%s%s%s%s' % (
            struct.pack('!i',room.id),
            struct.pack('!B',room.phase),
            struct.pack('!B',match_state),
            util.padWithZeros(room.name,64),
            struct.pack('!B',match_clock),
            ''.join(['%s%s%s%s%s%s%s' % (
                struct.pack('!i',usr.profile.id),
                struct.pack('!B',room.isOwner(usr)),
                # matchstarter or 1st host?
                struct.pack('!B',room.isMatchStarter(usr)), 
                struct.pack('!B',self.formatHomeOrAway(room, usr)), # team
                struct.pack('!B',usr.state.spectator), # spectator
                struct.pack('!B',room.getPlayerPosition(usr)), # pos in room
                struct.pack('!B',room.getPlayerParticipate(usr))) # participate
                for usr in room.players]),
            '\0\0\0\0\0\0\xff\0\0\xff'*(4-n), # empty players
            self.formatTeamsAndGoals(room),
            '\0', #padding
            struct.pack('!B', int(room.usePassword)), # room locked
            '\0\x02\0\0') # competition flag, match chat setting, 2 unknowns
            
    def formatRoomParticipationStatus(self, room):
        """
        Used to format the 0x4365 payload
        """
        
        n = len(room.players)
        data = '%s%s' % (
            ''.join(['%s%s%s' % (
                struct.pack('!i',usr.profile.id),
                struct.pack('!B',room.getPlayerPosition(usr)),
                struct.pack('!B',room.getPlayerParticipate(usr)))
                for usr in room.players]),
            '\0\0\0\0\0\xff'*(4-n))
        return data

    def becomeSpectator_4366(self, pkt):
        log.debug('pes6.becomeSpectator_4366')
        self._user.state.spectator = 1
        self.sendZeros(0x4367, 4)

    def do_4351(self, pkt):
        log.debug('pes6.do_4351')
        """
        Contains connection information of playing players
        Received from hosting player
        Send to possible spectators
        """
        data = '%s' % (pkt.data)
        room = self._user.state.room
        if room:
            spectatingPlayers = (player for player in room.players 
                if player not in room.participatingPlayers)
            for player in spectatingPlayers:
                player.sendData(0x4351, data)
        self.sendZeros(0x4352, 4)

    def backToMatchMenu_4383(self, pkt):
        log.debug('pes6.backToMatchMenu_4383')
        """
        Contains old,added,new points & rating
        For players and groups
        """
        room = self._user.state.room
        n = len(room.participatingPlayers)
        try:
          log.debug('backToMatchMenu_4383: room=%s' % room)
          match = room.match
          participants = [match.teamSelection.home_captain, match.teamSelection.away_captain]
          participants.extend(match.teamSelection.home_more_players)
          participants.extend(match.teamSelection.away_more_players)
          
          numParticipants = len(participants)
          
          for profile in participants:
            for usr in room.participatingPlayers:
              if usr.profile.id == profile.id:
                log.debug('backToMatchMenu_4383: matching id=%s' % profile.id)
                usr.profile.points = profile.points
                usr.profile.rating = profile.rating
                if numParticipants == 2:
                    usr.profile.lastPointsDiff = profile.lastPointsDiff
                    usr.profile.lastRatingDiff = profile.lastRatingDiff
                else:
                    usr.profile.lastPointsDiff = 0
                    usr.profile.lastRatingDiff = 0
                log.debug('backToMatchMenu_4383: after update: usr.profile.points=%s usr.profile.lastPointsDiff=%s usr.profile.rating=%s usr.profile.lastRatingDiff=%s' %(usr.profile.points,usr.profile.lastPointsDiff,usr.profile.rating,usr.profile.lastRatingDiff))
        except:
          log.msg("Error in backToMatchMenu_4383: %s" % sys.exc_info()[0])
          exc_type, exc_value, exc_traceback = sys.exc_info()
          lines = traceback.format_exception(exc_type, exc_value, exc_traceback)
          log.msg("Lines: %s" % lines)
            
        debugMode = self.factory.configuration.debugMode
    
        #if debugMode == 0:
        data = '\0\0\0\0%s%s%s' % (
            ''.join(['%s%s%s%s%s%s%s%s%s' % (
                struct.pack('!i',usr.profile.id),
                struct.pack('!H',0), # added points
                struct.pack('!i',usr.profile.points), # new points
                struct.pack('!H',0), # ?
                struct.pack('!H',0), # ?
                struct.pack('!H',0), # ?
                struct.pack('!H',0), # ?
                struct.pack('!H',usr.profile.rating), # new rating
                struct.pack('!H',usr.profile.rating-usr.profile.lastRatingDiff)) # old rating
                for usr in room.participatingPlayers]),
            '\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0'*(4-n),
            ''.join('%s%s%s%s%s%s' % (
                struct.pack('!i',0), # group1 id
                struct.pack('!H',0), # group1 added points
                struct.pack('!i',0), # group1 new points
                struct.pack('!i',0), # group2 id
                struct.pack('!H',0), # group2 added points
                struct.pack('!i',0)))) # group2 new points
        #else:
        #  data = '\0\0\0\0%s%s%s' % (
        #      ''.join(['%s%s%s%s%s%s%s%s%s' % (
        #          struct.pack('!i',usr.profile.id),
        #          struct.pack('!H',1), # added points
        #          struct.pack('!i',2), # new points
        #          struct.pack('!H',3), # ?
        #          struct.pack('!H',4), # ?
        #          struct.pack('!H',5), # ?
        #          struct.pack('!H',6), # ?
        #          struct.pack('!H',7), # new rating
        #          struct.pack('!H',8)) # old rating
        #          for usr in room.participatingPlayers]),
        #      '\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0\0'*(4-n),
        #      ''.join('%s%s%s%s%s%s' % (
        #          struct.pack('!i',0), # group1 id
        #          struct.pack('!H',0), # group1 added points
        #          struct.pack('!i',0), # group1 new points
        #          struct.pack('!i',0), # group2 id
        #          struct.pack('!H',0), # group2 added points
        #          struct.pack('!i',0)))) # group2 new points                
        self.sendData(0x4384, data)

    def quickGameSearch_6020(self, pkt):
        log.debug('pes6.quickGameSearch_6020')
        self.sendZeros(0x6021,0)

    def getStunInfo_4345(self, pkt):    
        log.debug('pes6.getStunInfo_4345')
        self.sendZeros(0x4346, 0)
        roomId = struct.unpack('!i',pkt.data[0:4])[0]
        thisLobby = self.factory.getLobbies()[self._user.state.lobbyId]
        room = thisLobby.getRoomById(roomId)
        if room is not None:
            # send stun info of players in room to requester
            for usr in room.players:
                data = ('%(pad1)s%(ip1)s%(port1)s'
                    '%(ip2)s%(port2)s%(id)s'
                    '%(someField)s%(participate)s') % {
                'pad1': '\0'*32,
                'ip1': util.padWithZeros(usr.state.ip1, 16),
                'port1': struct.pack('!H', usr.state.udpPort1),
                'ip2': util.padWithZeros(usr.state.ip2, 16),
                'port2': struct.pack('!H', usr.state.udpPort2),
                'id': struct.pack('!i', usr.profile.id),
                'someField': struct.pack('!H', 0),
                'participate': struct.pack('!B', 
                    room.getPlayerParticipate(usr)),
                }
                self.sendData(0x4347, data)
                self.do_4330(room)
        self.sendZeros(0x4348, 0)        

    def chat_4400(self, pkt):
        thisLobby = self.factory.getLobbies()[self._user.state.lobbyId]
        chatType = pkt.data[0:2]
        message = util.stripZeros(pkt.data[10:])
        data = '%s%s%s%s%s' % (
                chatType,
                pkt.data[2:6],
                struct.pack('!i',self._user.profile.id),
                util.padWithZeros(self._user.profile.name,48),
                #util.padWithZeros(message, 128))
                message[:126]+'\0\0')
        
        if chatType=='\x00\x01':
            # add to lobby chat history
            thisLobby.addToChatHistory(
                lobby.ChatMessage(self._user.profile, message))
            # lobby chat
            try:
                for usr in thisLobby.players.values():
                    usr.sendData(0x4402, data)
            except:
                log.msg("Error iterating in chat_4400: %s" % sys.exc_info()[0])
                exc_type, exc_value, exc_traceback = sys.exc_info()
                lines = traceback.format_exception(exc_type, exc_value, exc_traceback)
                log.msg("Lines: %s" % lines)
                
        elif chatType=='\x01\x08':
            # room chat
            room = self._user.state.room
            if room:
                for usr in room.players:
                    usr.sendData(0x4402, data)
            try:
                if room.name is not None:
                    log.msg('[CHAT] [%s] [Room] (%s) %s' % (room.name.decode('utf-8').encode("ascii","ignore"), self._user.profile.name, message.decode('utf-8').encode("ascii","ignore")))
            except:
                log.msg("Error in chat_4400 (room chat): %s" % sys.exc_info()[0])
                exc_type, exc_value, exc_traceback = sys.exc_info()
                lines = traceback.format_exception(exc_type, exc_value, exc_traceback)
                # log.msg("Lines: %s" % lines)
                
        elif chatType=='\x00\x02':
            # private message
            profileId = struct.unpack('!i',pkt.data[6:10])[0]
            usr = thisLobby.getPlayerByProfileId(profileId)
            if usr:
                # add to lobby chat history
                thisLobby.addToChatHistory(
                    lobby.ChatMessage(
                        self._user.profile, message, usr.profile,
                        pkt.data[2:6]))
                usr.sendData(0x4402, data)
                if usr != self._user:
                    self._user.sendData(0x4402, data) # echo to self
            else:
                log.msg(
                    'WARN: user with profile id = '
                    '%d not found.' % profileId)
                
        elif chatType=='\x01\x05':
            # match chat
            room = self._user.state.room
            if room:
                for usr in room.players:
                    usr.sendData(0x4402, data)
                
            try:
                if room.name is not None:
                    log.msg('[CHAT] [%s] [Match] (%s) %s' % (room.name.decode('utf-8').encode("ascii","ignore"), self._user.profile.name, message.decode('utf-8').encode("ascii","ignore")))
            except:
                log.msg("Error in chat_4400 (match chat): %s" % sys.exc_info()[0])
                exc_type, exc_value, exc_traceback = sys.exc_info()
                lines = traceback.format_exception(exc_type, exc_value, exc_traceback)
                # log.msg("Lines: %s" % lines)
        elif chatType=='\x01\x07':
            # stadium chat    
            room = self._user.state.room
            if room:
                for usr in room.players:
                    usr.sendData(0x4402, data)
            
            try:
                if room.name is not None:
                    log.msg('[CHAT] [%s] [Stadium] (%s) %s' % (room.name.decode('utf-8').encode("ascii","ignore"), self._user.profile.name, message.decode('utf-8').encode("ascii","ignore")))
            except:
                log.msg("Error in chat_4400 (stadium chat): %s" % sys.exc_info()[0])
                exc_type, exc_value, exc_traceback = sys.exc_info()
                lines = traceback.format_exception(exc_type, exc_value, exc_traceback)
                log.msg("Lines: %s" % lines)


    def sendChatHistory(self, aLobby, who):
        if aLobby is None or who is None:
            return
        for chatMessage in list(aLobby.chatHistory):
            chatType = '\0\1'
            if chatMessage.toProfile is not None:
                if who.profile.id not in [
                    chatMessage.fromProfile.id, chatMessage.toProfile.id]:
                    continue
                special = chatMessage.special
            else:
                special = '\0\0\0\0'
            data = '%s%s%s%s%s' % (
                    chatType,
                    special,
                    struct.pack('!i', chatMessage.fromProfile.id),
                    util.padWithZeros(chatMessage.fromProfile.name,48),
                    chatMessage.text[:126]+'\0\0')
            who.sendData(0x4402, data)

    def broadcastSystemChat(self, aLobby, text):
        chatMessage = lobby.ChatMessage(lobby.SYSTEM_PROFILE, text)
        try:
          for usr in aLobby.players.values():
              data = '%s%s%s%s%s' % (
                      '\0\1',
                      '\0\0\0\0',
                      struct.pack('!i', chatMessage.fromProfile.id),
                      util.padWithZeros(chatMessage.fromProfile.name,48),
                      chatMessage.text[:126]+'\0\0')
              usr.sendData(0x4402, data)
        except:
            log.msg("Error iterating in broadcastSystemChat: %s" % sys.exc_info()[0])
            exc_type, exc_value, exc_traceback = sys.exc_info()
            lines = traceback.format_exception(exc_type, exc_value, exc_traceback)
            log.msg("Lines: %s" % lines)
        
        aLobby.addToChatHistory(chatMessage)

    def broadcastRoomChat(self, room, text):
        chatMessage = lobby.ChatMessage(lobby.SYSTEM_PROFILE, text)
        for usr in room.players:
            data = '%s%s%s%s%s' % (
                    '\x01\x08',
                    '\0\0\0\0',
                    struct.pack('!i', chatMessage.fromProfile.id),
                    util.padWithZeros(chatMessage.fromProfile.name,48),
                    chatMessage.text[:126]+'\0\0')
            usr.sendData(0x4402, data)
         
    def sendRoomUpdate(self, room):
        log.debug('sendRoomUpdate')
        thisLobby = self.factory.getLobbies()[self._user.state.lobbyId]
        data = self.formatRoomInfo(room)
        try:
            log.debug('sendRoomUpdate: room.name=%s' % room.name)
            for key, usr in thisLobby.players.iteritems():
                # log.msg('sendRoomUpdate: key=%s profiles=%s' % (key, ','.join([x.name for x in usr.profiles])))
                usr.sendData(0x4306,data)
        except:
            log.msg("Error iterating in sendRoomUpdate: %s" % sys.exc_info()[0])
            exc_type, exc_value, exc_traceback = sys.exc_info()
            lines = traceback.format_exception(exc_type, exc_value, exc_traceback)
            log.msg("Lines: %s" % lines)

    @defer.inlineCallbacks
    def sendPlayerUpdate(self, roomId):
        log.debug('sendPlayerUpdate')
        thisLobby = self.factory.getLobbies()[self._user.state.lobbyId]
        stats = yield self.getStats(self._user.profile.id)
        data = self.formatPlayerInfo(self._user, roomId, stats)
        try:
          for usr in thisLobby.players.values():
              usr.sendData(0x4222,data)
        except:
          log.msg("Error iterating in sendPlayerUpdate: %s" % sys.exc_info()[0])
          exc_type, exc_value, exc_traceback = sys.exc_info()
          lines = traceback.format_exception(exc_type, exc_value, exc_traceback)
          log.msg("Lines: %s" % lines)

    @defer.inlineCallbacks
    def getUserList_4210(self, pkt):
        log.debug('pes6.getUserList_4210')
        self.sendZeros(0x4211,4)
        thisLobby = self.factory.getLobbies()[self._user.state.lobbyId]
        try:
          for usr in thisLobby.players.values():
              if usr.state.inRoom == 1:
                  roomId = usr.state.room.id
              else:
                  roomId = 0
              stats = yield self.getStats(usr.profile.id)
              data = self.formatPlayerInfo(usr, roomId, stats)
              self.sendData(0x4212,data)
        except:
            log.msg("Error iterating in getUserList_4210: %s" % sys.exc_info()[0])
            exc_type, exc_value, exc_traceback = sys.exc_info()
            lines = traceback.format_exception(exc_type, exc_value, exc_traceback)
            log.msg("Lines: %s" % lines)
        
        self.sendZeros(0x4213,4)
        yield defer.succeed(None)

    @defer.inlineCallbacks
    def createRoom_4310(self, pkt):
        log.debug('pes6.createRoom_4310')
        # banned?
        try:
            banned = yield self.factory.matchData.CheckBanned(self._user.profile.id)
            log.debug("(createRoom_4310) User=%s banned=%s" % (self._user.profile.id, banned))
            if banned > 0:
                log.msg("(createRoom_4310) User banned! ID=%s Name=%s" % (self._user.profile.id, self._user.profile.name))
                return
            else:
                maintenance = self.factory.configuration.maintenance
                if maintenance > 0:
                    log.msg("(createRoom_4310) maintenance=%s, trying to disconnect" % maintenance)
                    try:
                        self.transport.loseConnection('maintenance mode')
                    except:
                        log.msg("(createRoom_4310) diconnecting failed")
                    return
        except:
            log.msg("Error in createRoom_4310: %s" % sys.exc_info()[0])
            exc_type, exc_value, exc_traceback = sys.exc_info()
            lines = traceback.format_exception(exc_type, exc_value, exc_traceback)
            log.msg("Lines: %s" % lines)
        
        thisLobby = self.factory.getLobbies()[self._user.state.lobbyId]
        roomName = util.stripZeros(pkt.data[0:64])
        try: 
            existing = thisLobby.getRoom(roomName)
            self.sendData(0x4311,'\xff\xff\xff\x10')
        except KeyError:
            room = lobby.Room(thisLobby)
            room.name = roomName
            room.usePassword = struct.unpack('!B',pkt.data[64:65])[0] == 1
            if room.usePassword:
                room.password = util.stripZeros(pkt.data[65:80])
            # put room creator into the room
            room.enter(self._user)
            # add room to the lobby
            thisLobby.addRoom(room)
            log.msg('Room created: %s' % str(room))
            try:
                log.msg('Room created=%s user=%s profiles=%s ip=%s' % (str(room), self._user.username, ','.join([x.name for x in self._user.profiles]), self.addr.host))
            except:
                log.msg("Error in createRoom_4310: %s" % sys.exc_info()[0])
                exc_type, exc_value, exc_traceback = sys.exc_info()
                lines = traceback.format_exception(exc_type, exc_value, exc_traceback)
                log.msg("Lines: %s" % lines)
            # notify all users in the lobby about the new room
            self.sendRoomUpdate(room)
            # notify all users in the lobby that player is now in a room
            self.sendPlayerUpdate(room.id)
            self.sendZeros(0x4311,4)
        
    def getRoomList_4300(self, pkt):
        log.debug('pes6.getRoomList_4300')
        self.sendZeros(0x4301,4)
        thisLobby = self.factory.getLobbies()[self._user.state.lobbyId]
        for room in thisLobby.rooms.itervalues():
            data = self.formatRoomInfo(room)
            self.sendData(0x4302, data)
        self.sendZeros(0x4303,4)

    def setOwner_4349(self, pkt):
        log.debug('pes6.setOwner_4349')
        newOwnerProfileId = struct.unpack('!i',pkt.data[0:4])[0]
        thisLobby = self.factory.getLobbies()[self._user.state.lobbyId]
        room = self._user.state.room
        if room:
            usr = thisLobby.getPlayerByProfileId(newOwnerProfileId)
            if not usr:
                log.msg('WARN: player %s cannot become owner: not in the room.')
            else:
                room.setOwner(usr)
                self.sendRoomUpdate(room)
        self.sendZeros(0x434a,4)

    def setRoomName_434d(self, pkt):
        log.debug('pes6.setRoomName_434d')
        newName = util.stripZeros(pkt.data[0:63])
        thisLobby = self.factory.getLobbies()[self._user.state.lobbyId]
        room = self._user.state.room
        data = '\0\0\0\0'
        if room:
            if newName != room.name:
                # prevent renaming to existing rooms
                if thisLobby.isRoom(newName):
                    data = '\xff\xff\xff\xff'
                else:
                    thisLobby.renameRoom(room, newName)
            room.usePassword = struct.unpack('!B',pkt.data[64:65])[0] == 1
            if room.usePassword:
                room.password = util.stripZeros(pkt.data[65:80])            
            self.sendRoomUpdate(room)
        self.sendData(0x434e,data)
        
    def do_4330(self, room):
        log.debug('pes6.do_4330')
        """
        Notify people INSIDE room of
        ip,ports and participation status
        """
        if room is None:
            log.msg('room is None in do_4330')
        else:
            for otherUsr in room.players:
                if otherUsr == self._user:
                    continue
                data = ('%(pad1)s%(ip1)s%(port1)s'
                '%(ip2)s%(port2)s%(id)s'
                '%(someField)s%(participate)s') % {
                'pad1': '\0'*36,
                'ip1': util.padWithZeros(self._user.state.ip1, 16),
                'port1': struct.pack('!H', self._user.state.udpPort1),
                'ip2': util.padWithZeros(self._user.state.ip2, 16),
                'port2': struct.pack('!H', self._user.state.udpPort2),
                'id': struct.pack('!i', self._user.profile.id),
                'someField': struct.pack('!H', 0),
                'participate': struct.pack('!B', 
                    room.getPlayerParticipate(self._user)),
                }
                otherUsr.sendData(0x4330, data)        

    @defer.inlineCallbacks
    def joinRoom_4320(self, pkt):
        # Banned?
        log.debug('pes6.joinRoom_4320')
        try:
            banned = yield self.factory.matchData.CheckBanned(self._user.profile.id)
            log.debug("(joinRoom_4320) User ID=%s banned=%s" % (self._user.profile.id, banned))
            if (banned > 0):
                log.msg("(joinRoom_4320) User banned! ID=%s Name=%s" % (self._user.profile.id, self._user.profile.name))
                return
            else:
                maintenance = self.factory.configuration.maintenance
                if maintenance > 0:
                    log.msg("(joinRoom_4320) maintenance=%s, trying to disconnect" % maintenance)
                    try:
                        self.transport.loseConnection('maintenance mode')
                    except:
                        log.msg("(joinRoom_4320) diconnecting failed")
                    return
        except:
            log.msg("Error in joinRoom_4320: %s" % sys.exc_info()[0])
            exc_type, exc_value, exc_traceback = sys.exc_info()
            lines = traceback.format_exception(exc_type, exc_value, exc_traceback)
            log.msg("Lines: %s" % lines)

        roomId = struct.unpack('!i',pkt.data[0:4])[0]
        thisLobby = self.factory.getLobbies()[self._user.state.lobbyId]
        room = thisLobby.getRoomById(roomId)
        if room is None:
            log.msg('ERROR: Room (id=%d) does not exist.' % roomId)
            self.sendData(0x4321,'\0\0\0\1')
        else:
            if room.usePassword:
                enteredPassword = util.stripZeros(pkt.data[4:19])
                if enteredPassword != room.password:
                    # log.debug('ERROR: Room (id=%d) password does not match.' % roomId)
                    self.sendData(0x4321,'\xff\xff\xfd\xda')
                else:
                    room.enter(self._user)
            else:
                room.enter(self._user)
                
            self.sendRoomUpdate(room)
            self.sendPlayerUpdate(room.id)
            data = '\0\0\0\0'
            if room.matchSettings:
                data += room.matchSettings.match_time
            self.sendData(0x4321, data)
        # give players in room stun of joiner
        # special 4330 packet
        self.do_4330(room)
        # give joiner stun of players in room
        self.sendZeros(0x4346, 0)
        if room is not None:
            for otherUsr in room.players:
                if otherUsr == self._user:
                    continue
                data = ('%(pad1)s%(ip1)s%(port1)s'
                '%(ip2)s%(port2)s%(id)s'
                '%(someField)s%(participate)s') % {
                'pad1': '\0'*32,
                'ip1': util.padWithZeros(otherUsr.state.ip1, 16),
                'port1': struct.pack('!H', otherUsr.state.udpPort1),
                'ip2': util.padWithZeros(otherUsr.state.ip2, 16),
                'port2': struct.pack('!H', otherUsr.state.udpPort2),
                'id': struct.pack('!i', otherUsr.profile.id),
                'someField': struct.pack('!H', 0),
                'participate': struct.pack('!B', 
                    room.getPlayerParticipate(otherUsr)),
                }
                self.sendData(0x4347, data)
        self.sendZeros(0x4348, 0)

    def exitingLobby(self, usr):
        log.debug('pes6.exitingLobby')
        usrLobby = self.factory.getLobbies()[usr.state.lobbyId]
        usrLobby.exit(usr)
        # user now considered OFFLINE
        log.debug('exitingLobby')
        self.factory.userOffline(usr)
        # notify every remaining occupant in the lobby
        try:
            for otherUsr in usrLobby.players.values():
                otherUsr.sendData(0x4221,struct.pack('!i', usr.profile.id))
        except:
            log.msg("Error iterating in exitingLobby: %s" % sys.exc_info()[0])
            exc_type, exc_value, exc_traceback = sys.exc_info()
            lines = traceback.format_exception(exc_type, exc_value, exc_traceback)
            log.msg("Lines: %s" % lines)
 
    def exitingRoom(self, room, usr):
        log.debug('pes6.exitingRoom')
        usrLobby = self.factory.getLobbies()[usr.state.lobbyId]
        try: 
            log.debug('usrLobby=%s' % usrLobby)
            log.debug('room=%s' % room)
            log.debug('usr profiles=%s' % ','.join([x.name for x in usr.profiles]))
        except:
            log.msg("Error iterating in exitingRoom: %s" % sys.exc_info()[0])
            exc_type, exc_value, exc_traceback = sys.exc_info()
            lines = traceback.format_exception(exc_type, exc_value, exc_traceback)
            log.msg("Lines: %s" % lines)
        room = usr.state.room
        try: 
            log.debug('room=%s' % room)
        except:
            log.msg("Error iterating in exitingRoom: %s" % sys.exc_info()[0])
            exc_type, exc_value, exc_traceback = sys.exc_info()
            lines = traceback.format_exception(exc_type, exc_value, exc_traceback)
            log.msg("Lines: %s" % lines)
        room.exit(usr)
        
        self.sendRoomUpdate(room)
        self.sendPlayerUpdate(room.id)
        self.sendZeros(0x432b,4)

        # destroy the room, if none left in it
        if room.isEmpty():
            # notify users in lobby that the room is gone
            data = struct.pack('!i',room.id)
            try:
                for otherUsr in usrLobby.players.values():
                    otherUsr.sendData(0x4305,data)
            except:
                log.msg("Error iterating in exitingRoom: %s" % sys.exc_info()[0])
                exc_type, exc_value, exc_traceback = sys.exc_info()
                lines = traceback.format_exception(exc_type, exc_value, exc_traceback)
                log.msg("Lines: %s" % lines)
            log.debug('deleting room')
            usrLobby.deleteRoom(room)

    def exitRoom_432a(self, pkt):
        log.debug('pes6.exitRoom_432a')
        try:
            if self._user.state.inRoom == 0:
                log.msg('WARN: user not in a room.')
                self.sendZeros(0x432b,4)
            else:
                return self.exitingRoom(
                    self._user.state.room, self._user)
        except:
            log.msg("ERROR in exitRoom_432a: %s" % sys.exc_info()[0])
            exc_type, exc_value, exc_traceback = sys.exc_info()
            lines = traceback.format_exception(exc_type, exc_value, exc_traceback)
            log.msg("Lines: %s" % lines)
                
    def toggleParticipate_4363(self, pkt):
        log.debug('pes6.toggleParticipate_4363')
        participate = (struct.unpack('!B', pkt.data[0])[0] == 1)
        room = self._user.state.room
        packetPayload = '\0\0\0\0' # success
        if room:
            if participate:
                # check roster-hash match with host
                rosterHashMismatch = False
                if room.participatingPlayers:
                    gameHost = room.participatingPlayers[0]
                    rosterHashMismatch = (
                        room.lobby.checkRosterHash and not self.checkHashes(
                            gameHost, self._user))
                if rosterHashMismatch:
                    packetPayload = '\0\0\0\1'
                    text = (
                        'Roster mismatch: %s vs %s. '
                        'You must use the same patch.' % (
                            gameHost.profile.name,
                            self._user.profile.name))
                    log.msg(text)
                    self.broadcastRoomChat(room, text.encode('utf-8'))
                elif room.isForcedCancelledParticipation(self._user):
                    packetPayload = '\xff\xff\xfd\xb6' # still cancelled
                else:
                    room.participate(self._user)
            else:
                room.cancelParticipation(self._user)
            # share participation status with players in room
            data = self.formatRoomParticipationStatus(room)
            for player in room.players:
                player.sendData(0x4365, data)
        data = '%s%s%s' % (
               packetPayload,
               struct.pack('!B', participate),
               struct.pack('!B', room.getPlayerParticipate(self._user)))
        self.sendData(0x4364, data)
        
    def forcedCancelParticipation_4380(self, pkt):
        log.debug('pes6.forcedCancelParticipation_4380')
        profileId = struct.unpack('!i',pkt.data[0:4])[0]
        thisLobby = self.factory.getLobbies()[self._user.state.lobbyId]
        room = self._user.state.room
        if room:
            usr = thisLobby.getPlayerByProfileId(profileId)
            room.cancelParticipation(usr)
            usr.state.timeCancelledParticipation = datetime.now()
            data = self.formatRoomParticipationStatus(room)
            for player in room.players:                
                player.sendData(0x4365, data)
        self.sendZeros(0x4381,4)


    def startMatch_4360(self, pkt):
        log.debug('pes6.startMatch_4360')
        thisLobby = self.factory.getLobbies()[self._user.state.lobbyId]
        room = self._user.state.room
        if room:
            data = '%s%s' % (
                '\x02',
                ''.join(['%s' % (
                    struct.pack('!i',usr.profile.id))
                    for usr in room.participatingPlayers]))
            data = util.padWithZeros(data, 37)        
            for player in room.players:
                player.sendData(0x4362, data)
            
            # Tell everyone of new phase of room
            room.phase = lobby.RoomState.ROOM_MATCH_SIDE_SELECT
            room.setMatchStarter(self._user)
            room.readyCount = 0
            room.lastUpdate = datetime.now()
            self.sendRoomUpdate(room)
        self.sendZeros(0x4361, 4)
        
    def updateRoomPhase(self, room):
        log.debug('pes6.updateRoomPhase')
        try:
            log.debug('updateRoomPhase room=%s' % room)
            log.debug('updateRoomPhase readyCount=%s' % room.readyCount)
            log.debug('updateRoomPhase participatingPlayers=%s' % len(room.participatingPlayers))
        except:
            log.msg("Error in updateRoomPhase: %s" % sys.exc_info()[0])
            exc_type, exc_value, exc_traceback = sys.exc_info()
            lines = traceback.format_exception(exc_type, exc_value, exc_traceback)
            log.msg("Lines: %s" % lines)            
            
        if room.readyCount == len(room.participatingPlayers):
            room.phase += 1
            
            data = struct.pack('B', room.phase)
            for usr in room.players:
                usr.sendData(0x4344, data)
            # reset count
            room.readyCount = 0
            room.lastUpdate = datetime.now()
            # Tell everyone of new phase of room
            self.sendRoomUpdate(room)
        
    def toggleReady_436f(self, pkt):
        log.debug('pes6.toggleReady_436f')
        payload = struct.unpack('!B', pkt.data[0])[0]
        room = self._user.state.room
        try:
            log.debug('toggleReady_436f: profileId=%s room=%s' % (self._user.profile.id, room))
        except:
            log.msg("Error in toggleReady_436f: %s" % sys.exc_info()[0])
            exc_type, exc_value, exc_traceback = sys.exc_info()
            lines = traceback.format_exception(exc_type, exc_value, exc_traceback)
            log.msg("Lines: %s" % lines)    
        if room:
            room.lastUpdate = datetime.now()
            # phase 2-6: match not really started
            if room.isAtPregameSettings(room):
                if payload == 1:
                    room.readyCount += 1
                elif payload == 0:
                    room.readyCount -= 1
            
            # phase 7-8: match finished
            elif room.phase > lobby.RoomState.ROOM_MATCH_FORMATION_SELECT:
                # exit match
                if payload == 0:
                    room.cancelParticipation(self._user)
                    if len(room.participatingPlayers) == 0:
                        room.phase = lobby.RoomState.ROOM_IDLE
                        log.debug('toggleReady_436f: room.match=None (exit match) room=%s' % room)
                        room.match = None
                    else: # there's still a player in the endmatch screen
                        room.phase = lobby.RoomState.ROOM_MATCH_SERIES_ENDING
                # play again different teams
                elif payload == 3:
                    room.phase = lobby.RoomState.ROOM_MATCH_TEAM_SELECT
                    log.debug('toggleReady_436f: room.match=None (play again different teams) room=%s' % room)
                    room.match = None
                # play again same teams
                elif payload == 4:
                    room.phase = lobby.RoomState.ROOM_MATCH_FORMATION_SELECT
                    log.debug('toggleReady_436f: room.match=None (play again same teams) room=%s' % room)
                    room.match = None
                self.sendRoomUpdate(room)
                    
            for usr in room.players:
                if usr == self._user:
                    continue
                data = '%s%s' % (
                    struct.pack('!i',self._user.profile.id),
                    pkt.data[0])
                usr.sendData(0x4371, data)
        self.sendZeros(0x4370,4)

        # if all participating players are ready, next screen
        if room.isAtPregameSettings(room):
            self.updateRoomPhase(room)

    @defer.inlineCallbacks
    def setPlayerSettings_4369(self, pkt):
        log.debug('pes6.setPlayerSettings_4369')
        # Packet contains which players are in team1 & team2
        self.sendZeros(0x436a, 4)
        room = self._user.state.room
        for usr in room.players:
            data = '%s%s' % (
                '\0',
                pkt.data)
            usr.sendData(0x436b, data)
        # create new TeamSelection object
        room.teamSelection = lobby.TeamSelection()
        setHomeCaptain = False
        for x in range(4):
            profile_id = struct.unpack('!i',pkt.data[x*8:x*8+4])[0]
            away = '\x01'==pkt.data[x*8+4]
            log.debug('profile_id=%s away=%s' % (profile_id, away))
            if profile_id!=0:
                profile = yield self.factory.getPlayerProfile(profile_id)
                if x in [0,1]:
                    if not away:
                        if not setHomeCaptain:
                            setHomeCaptain = True
                            room.teamSelection.home_captain = profile
                            try:
                                log.debug('set room.teamSelection.home_captain=%s' % profile.name)
                            except:
                                log.msg('error logging room.teamSelection.home_captain')
                        else:
                            room.teamSelection.away_captain = profile
                            log.msg('WARNING: home_captain was already set! setting away_captain')
                            try:
                                log.debug('set room.teamSelection.away_captain=%s' % profile.name)
                            except:
                                log.msg('error logging room.teamSelection.away_captain')
                    else:
                        room.teamSelection.away_captain = profile
                        try:
                            log.debug('set room.teamSelection.away_captain=%s' % profile.name)
                        except:
                            log.msg('error logging room.teamSelection.away_captain')
                else:
                    if not away:
                        room.teamSelection.home_more_players.append(profile)
                        try:
                            log.debug('set room.teamSelection.home_more_players=%s' % profile.name)
                        except:
                            log.msg('error logging room.teamSelection.home_more_players')
                    else:
                        room.teamSelection.away_more_players.append(profile)
                        try:
                            log.debug('set room.teamSelection.away_more_players=%s' % profile.name)
                        except:
                            log.msg('error logging room.teamSelection.away_more_players')
                            
        self.sendRoomUpdate(room)

    def setGameSettings_436c(self, pkt):
        log.debug('pes6.setGameSettings_436c')
        # Packet contains game settings(time,injuries,penalty etcetera)
        self.sendZeros(0x436d, 4)
        room = self._user.state.room
        data = '%s' % (pkt.data)
        room.matchSettings = lobby.MatchSettings(*pkt.data)
        for usr in room.players:
            usr.sendData(0x436e, data)
        try:
            log.debug('data=%s' % data)
            log.debug('room.id=%s' % room.id)
            log.debug('room.name=%s' % room.name)
            log.debug('room.matchTime=%s' % room.matchTime)
            if room.matchSettings is not None:
                log.debug('room.matchSettings.match_time=%s' % binascii.b2a_hex(room.matchSettings.match_time))
                log.debug('room.matchSettings.time_limit=%s' % binascii.b2a_hex(room.matchSettings.time_limit))
                log.debug('room.matchSettings.number_of_pauses=%s' % binascii.b2a_hex(room.matchSettings.number_of_pauses))
                log.debug('room.matchSettings.condition=%s' % binascii.b2a_hex(room.matchSettings.condition))
                log.debug('room.matchSettings.injuries=%s' % binascii.b2a_hex(room.matchSettings.injuries))
                log.debug('room.matchSettings.max_no_of_substitutions=%s' % binascii.b2a_hex(room.matchSettings.max_no_of_substitutions))
                log.debug('room.matchSettings.match_type_ex=%s' % binascii.b2a_hex(room.matchSettings.match_type_ex))
                log.debug('room.matchSettings.match_type_pk=%s' % binascii.b2a_hex(room.matchSettings.match_type_pk))
                log.debug('room.matchSettings.time=%s' % binascii.b2a_hex(room.matchSettings.time))
                log.debug('room.matchSettings.season=%s' % binascii.b2a_hex(room.matchSettings.season))
                log.debug('room.matchSettings.weather=%s' % binascii.b2a_hex(room.matchSettings.weather))
        except:
            log.msg("Error in setGameSettings_436c: %s" % sys.exc_info()[0])
            exc_type, exc_value, exc_traceback = sys.exc_info()
            lines = traceback.format_exception(exc_type, exc_value, exc_traceback)
            log.msg("Lines: %s" % lines)            
        
        self.sendRoomUpdate(room)

    @defer.inlineCallbacks
    def goalScored_4375(self, pkt):
        log.debug('pes6.goalScored_4375')
        room = self._user.state.room
        if not room.match:
            log.msg('ERROR: Goal reported, but no match in the room.')
            log.msg('Trying to reattach match...')
            try: 
                thisLobby = self.factory.getLobbies()[self._user.state.lobbyId]
                roomName = room.name
                existing = thisLobby.getRoom(roomName)
                room.match = existing.match
                log.msg('roomName=%s' % roomName)
                log.msg('existing=%s' % existing)
                log.msg('existing.match=%s' % existing.match)
            except:
                log.msg("Error in goalScored_4375: %s" % sys.exc_info()[0])
                exc_type, exc_value, exc_traceback = sys.exc_info()
                lines = traceback.format_exception(exc_type, exc_value, exc_traceback)
                log.msg("Lines: %s" % lines)                
        if not room.match:
            log.msg('ERROR: Still no match in the room.')
        else:
            namesHome = '?'
            namesHome = getHomePlayerNames(room.match)
            namesAway = '?'
            namesAway = getAwayPlayerNames(room.match)
            teamIdHome = '?'
            if room.teamSelection.home_team_id is not None:
                teamIdHome = room.teamSelection.home_team_id
            teamIdAway = '?'
            if room.teamSelection.away_team_id is not None:
                teamIdAway = room.teamSelection.away_team_id
            if pkt.data[0] == '\0':
                log.msg('GOAL SCORED by HOME team %s (%s)' % (teamIdHome, namesHome))
                room.match.goalHome()
            else:
                log.msg('GOAL SCORED by AWAY team %s (%s)' % (teamIdAway, namesAway))
                room.match.goalAway()
            log.msg(
                'UPDATE: Team %s (%s) vs Team %s (%s) - %d:%d (in progress)' % (
                    teamIdHome, namesHome, teamIdAway, namesAway, room.match.score_home, room.match.score_away))
        try:
            profileHome = room.match.teamSelection.home_captain.id
            profileHome2, profileHome3 = getHomePlayerIdsMore(room.match)
            profileAway = room.match.teamSelection.away_captain.id
            profileAway2, profileAway3 = getAwayPlayerIdsMore(room.match)
            scoreHome = room.match.score_home
            scoreAway = room.match.score_away
            scoreHomeReg = room.match.score_home_reg
            scoreAwayReg = room.match.score_away_reg
            
            if room.match.id == -1:
                log.msg('room.match.id=-1, getting id')
                room.match.id = yield self.factory.matchData.GetMatchId(profileHome, profileHome2, profileAway, profileAway2)
                log.msg('retrieved room.match.id=%s' % room.match.id)
            if room.match.id > 0:
                log.debug("scoreHome=%s, scoreAway=%s, profileHome=%s, profileHome2=%s, profileAway=%s, profileAway2=%s" % (scoreHome, scoreAway, profileHome, profileHome2, profileAway, profileAway2))
                self.factory.matchData.MatchStatusUpdateGoal(scoreHome, scoreAway, scoreHomeReg, scoreAwayReg, room.match.id)
        except:
            log.msg("Error calling MatchStatusUpdateGoal: %s" % sys.exc_info()[0])
            exc_type, exc_value, exc_traceback = sys.exc_info()
            lines = traceback.format_exception(exc_type, exc_value, exc_traceback)
            log.msg("Lines: %s" % lines)
        
        self.sendZeros(0x4376, 4)
        # let others in the lobby know
        self.sendRoomUpdate(room)

    @defer.inlineCallbacks
    def matchClockUpdate_4385(self, pkt):
        log.debug('pes6.matchClockUpdate_4385')
        clock = struct.unpack('!B', pkt.data[0])[0]
        try:
            log.debug('clock: %s' % clock)
        except:
            log.msg('ERROR logging clock')
        
        room = self._user.state.room
        if room and not room.match:
            log.msg('ERROR: got clock update, but no match')
            log.msg('Trying to reattach match...')
        try: 
            thisLobby = self.factory.getLobbies()[self._user.state.lobbyId]
            roomName = room.name
            existing = thisLobby.getRoom(roomName)
            room.match = existing.match
        except:
            log.msg("Error in matchClockUpdate_4385: %s" % sys.exc_info()[0])
            exc_type, exc_value, exc_traceback = sys.exc_info()
            lines = traceback.format_exception(exc_type, exc_value, exc_traceback)
            log.msg("Lines: %s" % lines)                

        if not room or not room.match:
            log.msg('ERROR: got clock update, but no match or room')
            try:
                log.debug('This user is: {%s} (profiles: %s)' % (self._user.hash,','.join([x.name for x in self._user.profiles])))
                log.debug('self._user.state=%s' % self._user.state)
                log.debug('self._user.state.room=%s' % self._user.state.room)
            except:
                log.msg("Error in matchClockUpdate_4385: %s" % sys.exc_info()[0])
                exc_type, exc_value, exc_traceback = sys.exc_info()
                lines = traceback.format_exception(exc_type, exc_value, exc_traceback)
                log.msg("Lines: %s" % lines)
        else:
            room.match.clock = clock
            match = room.match
            try:
                teamHome = '?'
                if match.teamSelection is not None:
                    if match.teamSelection.home_team_id is not None:
                        teamHome = match.teamSelection.home_team_id
                teamAway = '?'
                if match.teamSelection is not None:
                    if match.teamSelection.away_team_id is not None:
                        teamAway = match.teamSelection.away_team_id
                log.msg('CLOCK: Team %s (%s) vs Team %s (%s). Minute: %d' % (
                    teamHome, 
                    getHomePlayerNames(room.match),
                    teamAway, 
                    getAwayPlayerNames(room.match),
                    room.match.clock))
            except:
                log.msg("Error in matchClockUpdate_4385: %s" % sys.exc_info()[0])
                exc_type, exc_value, exc_traceback = sys.exc_info()
                lines = traceback.format_exception(exc_type, exc_value, exc_traceback)
                log.msg("Lines: %s" % lines)
        try:
            matchStateText = MatchState.stateText.get(room.match.state, 'Unknown')
            profileHome = room.match.teamSelection.home_captain.id
            profileHome2, profileHome3 = getHomePlayerIdsMore(room.match)
            profileAway = room.match.teamSelection.away_captain.id
            profileAway2, profileAway3 = getAwayPlayerIdsMore(room.match)
            if room.match.id == -1:
                log.msg('room.match.id=-1, getting id')
                room.match.id = yield self.factory.matchData.GetMatchId(profileHome, profileHome2, profileAway, profileAway2)
                log.msg('retrieved room.match.id=%s' % room.match.id)
            if room.match.id > 0:
                log.debug("clock=%s, matchStateText=%s, profileHome=%s, profileHome2=%s, profileAway=%s, profileAway2=%s" % (clock, matchStateText, profileHome, profileHome2, profileAway, profileAway2))
                self.factory.matchData.MatchStatusUpdate(clock, matchStateText, room.match.score_home, room.match.score_away, room.match.score_home_reg, room.match.score_away_reg, room.match.id)
        except:
            log.msg("Error calling MatchStatusUpdate: %s" % sys.exc_info()[0])
            exc_type, exc_value, exc_traceback = sys.exc_info()
            lines = traceback.format_exception(exc_type, exc_value, exc_traceback)
            log.msg("Lines: %s" % lines)
        
        self.sendZeros(0x4386, 4)
        # let others in the lobby know
        self.sendRoomUpdate(room)

    @defer.inlineCallbacks
    def recordMatchResult(self, room):
        log.debug('pes6.recordMatchResult')
        match = room.match
        duration = datetime.now() - match.startDatetime
        thisLobby = self.factory.getLobbies()[self._user.state.lobbyId]
        teamHome = '?'
        if match.teamSelection.home_team_id is not None:
            teamHome = match.teamSelection.home_team_id
        teamAway = '?'
        if match.teamSelection.away_team_id is not None:
            teamAway = match.teamSelection.away_team_id
        log.msg('MATCH FINISHED: '
                'Team %s (%s) - Team %s (%s)  %d:%d. '
                'Match time: %s. Lobby: %s' % (
            teamHome, getHomePlayerNames(match),
            teamAway, getAwayPlayerNames(match),
            match.score_home, match.score_away,
            duration, thisLobby.name))
        # check if match result should be stored
        if thisLobby.typeCode != 0x20: # no-stats
            # record the match in DB              
            hashHome = ''
            hashAway = ''
            roomName = ''
            try:
                hashHome = yield self.factory.getRosterHashForProfileId(match.teamSelection.home_captain.id)
                hashAway = yield self.factory.getRosterHashForProfileId(match.teamSelection.away_captain.id)
                roomName = room.name
            except:
                log.msg("recordMatchResult: Error: %s" % sys.exc_info()[0])
                exc_type, exc_value, exc_traceback = sys.exc_info()
                lines = traceback.format_exception(exc_type, exc_value, exc_traceback)
                log.msg("Lines: %s" % lines)
            
            season = self.factory.configuration.season
            matchId = yield self.factory.matchData.store(match, hashHome, hashAway, thisLobby.name, roomName, season)
            log.msg('recordMatchResult: stored match, id=%s' % matchId)
            
            # store settings
            try:
              if room.matchSettings is not None:
                matchStartTS = match.startDatetime.strftime('%Y-%m-%d %H:%M:%S')
                self.factory.matchData.MatchSetAdditionalInfo(matchId, 'F', room.matchSettings, matchStartTS, duration.seconds)
            except:
                log.msg("recordMatchResult: MatchSetAdditionalInfo: Error: %s" % sys.exc_info()[0])
                exc_type, exc_value, exc_traceback = sys.exc_info()
                lines = traceback.format_exception(exc_type, exc_value, exc_traceback)
                log.msg("Lines: %s" % lines)
            
            participants = [match.teamSelection.home_captain, match.teamSelection.away_captain]
            participants.extend(match.teamSelection.home_more_players)
            participants.extend(match.teamSelection.away_more_players)
            
            numParticipants = len(participants)
            
            log.msg('pes6.py: recordMatchResult: numParticipants=%s' % numParticipants)
            
            for profile in participants:
                # update player play time
                profile.playTime += duration
                # re-calculate points
                stats = yield self.getStats(profile.id)
                rm = self.factory.ratingMath
                newPoints = rm.getPoints(stats, profile.disconnects)
                profile.lastPointsDiff = newPoints-profile.points
                log.msg("recordMatchResult: profile.id=%s profile.name=%s profile.points=%s newPoints=%s profile.lastPointsDiff=%s" % (profile.id, profile.name, profile.points, newPoints, profile.lastPointsDiff))
                profile.points = newPoints
                try:
                  newRating = rm.getRating(stats, profile.disconnects)
                  profile.lastRatingDiff = newRating-profile.rating
                  log.msg("recordMatchResult: profile.id=%s profile.name=%s profile.rating=%s newRating=%s profile.lastRatingDiff=%s" % (profile.id, profile.name, profile.rating, newRating, profile.lastRatingDiff))
                  profile.rating = newRating
                except:
                  log.msg("Error getting rating in pes6.recordMatchResult: %s" % sys.exc_info()[0])
                  exc_type, exc_value, exc_traceback = sys.exc_info()
                  lines = traceback.format_exception(exc_type, exc_value, exc_traceback)
                  log.msg("Lines: %s" % lines)
                # store updated profile
                yield self.factory.storeProfile(profile)
                # update match
                self.factory.matchData.UpdateMatchPointsAndRating(matchId, profile.id, profile.points, profile.lastPointsDiff, profile.rating, profile.lastRatingDiff)
            
        else:
          log.msg("Not saving match %s - %s" % (getHomePlayerNames(match),getAwayPlayerNames(match)))
          yield defer.succeed(None)

        try:
          profileHome = room.match.teamSelection.home_captain.id
          profileHome2, profileHome3 = getHomePlayerIdsMore(room.match)
          profileAway = room.match.teamSelection.away_captain.id
          profileAway2, profileAway3 = getAwayPlayerIdsMore(room.match)
          if room.match.id == -1:
              log.msg('room.match.id=-1, getting id')
              room.match.id = yield self.factory.matchData.GetMatchId(profileHome, profileHome2, profileAway, profileAway2)
              log.msg('retrieved room.match.id=%s' % room.match.id)
          if room.match.id > 0:
              log.debug("profileHome=%s, profileHome2=%s, profileAway=%s, profileAway2=%s" % 
                (profileHome, profileHome2, profileAway, profileAway2))
              self.factory.matchData.MatchStatusDelete(room.match.id)
        except:
          log.msg("Error calling MatchStatusDelete: %s" % sys.exc_info()[0])
          exc_type, exc_value, exc_traceback = sys.exc_info()
          lines = traceback.format_exception(exc_type, exc_value, exc_traceback)
          log.msg("Lines: %s" % lines)
        
        try:
          if self.factory.configuration.maintenance > 0:
            log.msg('maintenance, disconnecting after match')
            self.transport.loseConnection('maintenance mode')
        except:
          log.msg("(pes6.recordMatchResult) disconnecting failed: %s" % sys.exc_info()[0])
          exc_type, exc_value, exc_traceback = sys.exc_info()
          lines = traceback.format_exception(exc_type, exc_value, exc_traceback)
          log.msg("Lines: %s" % lines)
        return

    @defer.inlineCallbacks
    def matchStateUpdate_4377(self, pkt):
        log.debug('pes6.matchStateUpdate_4377')
        try:
            state = struct.unpack('!B', pkt.data[0])[0]
            room = self._user.state.room
            if not room or not room.teamSelection:
                log.msg(
                    'ERROR: got match state update, '
                    'but no room or team-selection')
            else:
                if room.match is not None:
                    room.match.state = state
                # check if match just started
                log.debug('matchStateUpdate_4377: state=%s' % state)
                if state == lobby.MatchState.FIRST_HALF:
                    match = lobby.Match6(room.teamSelection)

                    log.debug('matchStateUpdate_4377: match created')

                    homePlayerNames = ""
                    awayPlayerNames = ""
                    log.debug('NEW MATCH started')
                    try:
                        homePlayerNames = getHomePlayerNames(room)
                        awayPlayerNames = getAwayPlayerNames(room)
                        log.debug('home_team_id=%d' % room.teamSelection.home_team_id)
                        log.debug('homePlayerNames=%s' % homePlayerNames)
                        log.debug('away_team_id=%d' % room.teamSelection.away_team_id)
                        log.debug('awayPlayerNames=%s' % awayPlayerNames)
                    except:
                        log.msg("matchStateUpdate_4377: Error: %s" % sys.exc_info()[0])
                        exc_type, exc_value, exc_traceback = sys.exc_info()
                        lines = traceback.format_exception(exc_type, exc_value, exc_traceback)
                        log.msg("Lines: %s" % lines)

                    match.startDatetime = datetime.now()
                    match.home_team_id = match.teamSelection.home_team_id
                    match.away_team_id = match.teamSelection.away_team_id
                    room.match = match
                    room.match.state = state
                    
                    try:
                        if room.matchSettings is not None:
                            log.debug('room.matchSettings.match_time=%s' % binascii.b2a_hex(room.matchSettings.match_time))
                            log.debug('room.matchSettings.time_limit=%s' % binascii.b2a_hex(room.matchSettings.time_limit))
                            log.debug('room.matchSettings.number_of_pauses=%s' % binascii.b2a_hex(room.matchSettings.number_of_pauses))
                            log.debug('room.matchSettings.condition=%s' % binascii.b2a_hex(room.matchSettings.condition))
                            log.debug('room.matchSettings.injuries=%s' % binascii.b2a_hex(room.matchSettings.injuries))
                            log.debug('room.matchSettings.max_no_of_substitutions=%s' % binascii.b2a_hex(room.matchSettings.max_no_of_substitutions))
                            log.debug('room.matchSettings.match_type_ex=%s' % binascii.b2a_hex(room.matchSettings.match_type_ex))
                            log.debug('room.matchSettings.match_type_pk=%s' % binascii.b2a_hex(room.matchSettings.match_type_pk))
                            log.debug('room.matchSettings.time=%s' % binascii.b2a_hex(room.matchSettings.time))
                            log.debug('room.matchSettings.season=%s' % binascii.b2a_hex(room.matchSettings.season))
                            log.debug('room.matchSettings.weather=%s' % binascii.b2a_hex(room.matchSettings.weather))
                    except:
                        log.msg("matchStateUpdate_4377: Error: %s" % sys.exc_info()[0])
                        exc_type, exc_value, exc_traceback = sys.exc_info()
                        lines = traceback.format_exception(exc_type, exc_value, exc_traceback)
                        log.msg("Lines: %s" % lines)
                        
                    lobbyName = ''
                    hashHome = ''
                    hashAway = ''
                    try:
                        hashHome = yield self.factory.getRosterHashForProfileId(match.teamSelection.home_captain.id)
                        hashAway = yield self.factory.getRosterHashForProfileId(match.teamSelection.away_captain.id)
                        thisLobby = self.factory.getLobbies()[self._user.state.lobbyId]
                        lobbyName = thisLobby.name
                    except:
                        log.msg("matchStateUpdate_4377: Error: %s" % sys.exc_info()[0])
                        exc_type, exc_value, exc_traceback = sys.exc_info()
                        lines = traceback.format_exception(exc_type, exc_value, exc_traceback)
                        log.msg("Lines: %s" % lines)
                    
                    matchId = -1
                    try:
                      matchStateText = MatchState.stateText.get(room.match.state, 'Unknown')
                      profileHome = room.match.teamSelection.home_captain.id
                      profileHome2, profileHome3 = getHomePlayerIdsMore(room.match)
                      profileAway = room.match.teamSelection.away_captain.id
                      profileAway2, profileAway3 = getAwayPlayerIdsMore(room.match)
                      season = self.factory.configuration.season
                      log.debug("matchStateText=%s profileHome=%s profileHome2=%s profileHome3=%s profileAway=%s profileAway2=%s profileAway3=%s hashHome=%s hashAway=%s teamHome=%s teamAway=%s lobbyName=%s season=%s" % (matchStateText, profileHome, profileHome2, profileHome3, profileAway, profileAway2, profileAway3, hashHome, hashAway, match.home_team_id, match.away_team_id, lobbyName, season))
                      matchId = yield self.factory.matchData.MatchStatusInsert(matchStateText, profileHome, profileHome2, profileHome3, profileAway, profileAway2, profileAway3, hashHome, hashAway, match.home_team_id, match.away_team_id, lobbyName, season)
                      room.match.id = matchId
                    except:
                      log.msg("Error calling MatchStatusInsert: %s" % sys.exc_info()[0])
                      exc_type, exc_value, exc_traceback = sys.exc_info()
                      lines = traceback.format_exception(exc_type, exc_value, exc_traceback)
                      log.msg("Lines: %s" % lines)

                    # store settings
                    try:
                      if room.matchSettings is not None:
                        matchStartTS = match.startDatetime.strftime('%Y-%m-%d %H:%M:%S')
                        self.factory.matchData.MatchSetAdditionalInfo(matchId, 'U', room.matchSettings, matchStartTS, 0)
                    except:
                        log.msg("matchStateUpdate_4377: MatchSetAdditionalInfo: Error: %s" % sys.exc_info()[0])
                        exc_type, exc_value, exc_traceback = sys.exc_info()
                        lines = traceback.format_exception(exc_type, exc_value, exc_traceback)
                        log.msg("Lines: %s" % lines)

                # check if match is done
                elif state == lobby.MatchState.FINISHED and room.match:
                    room.phase = lobby.RoomState.ROOM_MATCH_FINISHED
                    self.recordMatchResult(room)
                # let others in the lobby know
                self.sendRoomUpdate(room)
        except:
            log.msg("matchStateUpdate_4377: Error: %s" % sys.exc_info()[0])
            exc_type, exc_value, exc_traceback = sys.exc_info()
            lines = traceback.format_exception(exc_type, exc_value, exc_traceback)
            log.msg("Lines: %s" % lines)
        self.sendZeros(0x4378, 4)

    def teamSelected_4373(self, pkt):
        log.debug('pes6.teamSelected_4373')
        team = struct.unpack('!H', pkt.data[0:2])[0]
        log.debug('Team selected: %d' % team)
        room = self._user.state.room
        if not room.teamSelection:
            log.msg('ERROR: room has no TeamSelection object')
        else:
            try:
                ts = room.teamSelection
                log.debug('This user is %s=%s' % (self._user.profile.id, self._user.profile.name))
                if ts.home_captain is None:
                    log.msg("ts.home_captain is None!")
                elif self._user.profile.id == ts.home_captain.id:
                    log.debug("setting home_team_id: ts.home_captain.id=%s" % ts.home_captain.id)
                    ts.home_team_id = team
                
                if ts.away_captain is None:
                    log.msg("ts.away_captain is None!")
                elif self._user.profile.id == ts.away_captain.id:
                    log.debug("setting away_team_id: ts.away_captain.id=%s" % ts.away_captain.id)
                    ts.away_team_id = team
            except:
                log.msg("ERROR in teamSelected_4373: %s" % sys.exc_info()[0])
                exc_type, exc_value, exc_traceback = sys.exc_info()
                lines = traceback.format_exception(exc_type, exc_value, exc_traceback)
                log.msg("Lines: %s" % lines)
                log.msg('Trying fallback solution')
                try:
                    if ((ts.home_captain is not None and (self._user.profile.id == ts.home_captain.id)) or 
                        any(prof for prof in ts.home_more_players 
                            if prof.id == self._user.profile.id)):
                        ts.home_team_id = team
                        log.debug('set home_team_id')
                    elif ((ts.away_captain is not None and (self._user.profile.id == ts.away_captain.id)) or 
                        any(prof for prof in ts.away_more_players 
                            if prof.id == self._user.profile.id)):
                        ts.away_team_id = team
                        log.debug('set away_team_id')
                except:
                    log.msg("ERROR in teamSelected_4373 - fallback solution: %s" % sys.exc_info()[0])
                    exc_type, exc_value, exc_traceback = sys.exc_info()
                    lines = traceback.format_exception(exc_type, exc_value, exc_traceback)
                    log.msg("Lines: %s" % lines)

        self.sendData(0x4374,'\0\0\0\0')
        self.sendRoomUpdate(room)

    @defer.inlineCallbacks
    def setComment_4110(self, pkt):
        log.debug('pes6.setComment_4110')
        self._user.profile.comment = pkt.data
        yield self.factory.storeProfile(self._user.profile)
        self.sendZeros(0x4111,4)

    def relayRoomSettings_4350(self, pkt):
        log.debug('pes6.relayRoomSettings_4350')
        if not self._user.state:
            return
        room = self._user.state.room
        if room:
            if pkt.data[0:4] == '\0\0\1\3': #TODO clean this up (3 - phase?)
                # extract info that we care about
                room.matchTime = 5*(ord(pkt.data[12]) + 1)
                log.debug('relayRoomSettings_4350: match time set to: %d minutes' % room.matchTime)
            # send to others
            for usr in self._user.state.room.players:
                if usr == self._user:
                    continue
                usr.sendData(0x4350, pkt.data)

    def do_3087(self, pkt):
        log.debug('pes6.do_3087 (dc)')
        try:
            # also sent at 'Exit match series'
            if self._user.state is not None:
                room = self._user.state.room
                log.debug('do_3087: user=%s' % self._user)
                if room and room.match is not None:
                    # remove match reference from room data structure
                    # is this room empty?
                    log.debug('do_3087: match=%s' % room.match)
                    if room.match.teamSelection:
                        for prof in self._user.profiles:
                            if (room.match.teamSelection.home_captain.name == prof.name):
                                log.msg('do_3087: home_captain exiting, profile=%s' % prof.name)
                                room.match.home_exit = 1
                            elif (room.match.teamSelection.away_captain.name == prof.name):
                                log.msg('do_3087: away_captain exiting, profile=%s' % prof.name)
                                room.match.away_exit = 1
                    
                    if room.match.id > 0:
                        if room.match.home_exit == 1:
                            log.msg('do_3087: setting home exit, match=%s' % room.match.id)
                            self.factory.matchData.MatchStatusSetHomeExit(room.match.id)
                        if room.match.away_exit == 1:
                            log.msg('do_3087: setting away exit, match=%s' % room.match.id)
                            self.factory.matchData.MatchStatusSetAwayExit(room.match.id)

                    numParticipating = 0
                    if room.participatingPlayers:
                        numParticipating = len(room.participatingPlayers)
                        log.debug('do_3087: numParticipating=%s' % numParticipating)
                        if room.players:
                            numInRoom = len(room.players)
                            log.debug('do_3087: numInRoom=%s' % numInRoom)
                        for usr in room.players:
                            log.debug('do_3087: room.players=%s' % usr)
                        for usr in room.participatingPlayers:
                            log.debug('do_3087: room.participatingPlayers=%s' % usr)
                    else: 
                        log.debug('do_3087: room.participatingPlayers is None')
                    if (numParticipating == 0):
                        log.debug('do_3087: removing reference, Room=%s' % room)
                        match, room.match = room.match, None
                        duration = datetime.now() - match.startDatetime
        except:
            log.msg("ERROR in do_3087: %s" % sys.exc_info()[0])
            exc_type, exc_value, exc_traceback = sys.exc_info()
            lines = traceback.format_exception(exc_type, exc_value, exc_traceback)
            log.msg("Lines: %s" % lines)       

    def register(self):
        pes5.MainService.register(self)
        self.addHandler(0x6020, self.quickGameSearch_6020)
        self.addHandler(0x4110, self.setComment_4110)
        self.addHandler(0x4345, self.getStunInfo_4345)
        self.addHandler(0x4400, self.chat_4400)
        self.addHandler(0x4310, self.createRoom_4310)
        self.addHandler(0x4300, self.getRoomList_4300)
        self.addHandler(0x4320, self.joinRoom_4320)
        self.addHandler(0x4363, self.toggleParticipate_4363)
        self.addHandler(0x4360, self.startMatch_4360)
        self.addHandler(0x436f, self.toggleReady_436f)
        self.addHandler(0x4369, self.setPlayerSettings_4369)
        self.addHandler(0x436c, self.setGameSettings_436c)
        self.addHandler(0x4373, self.teamSelected_4373)
        self.addHandler(0x4375, self.goalScored_4375)
        self.addHandler(0x4377, self.matchStateUpdate_4377)
        self.addHandler(0x4385, self.matchClockUpdate_4385)
        self.addHandler(0x4349, self.setOwner_4349)
        self.addHandler(0x434d, self.setRoomName_434d)
        self.addHandler(0x4366, self.becomeSpectator_4366)
        self.addHandler(0x4351, self.do_4351)
        self.addHandler(0x4383, self.backToMatchMenu_4383)
        self.addHandler(0x4380, self.forcedCancelParticipation_4380)

