"""
User-related data
"""

from datetime import timedelta
import struct
from fiveserver import log
import util


class Profile:

    def __init__(self, index):
        self.index = index   # 1
        self.id = 0          # 4
        self.name = ''       # 16 bytes
        self.favPlayer = 0   # 2 bytes, PES5 only
        self.favTeam = 0     # 2 bytes, PES5 only
        self.points = 0      # 4 bytes
        self.disconnects = 0 # 2 bytes
        self.userId = None
        self.rank = 0
        self.rating = 0 # PES6 only
        self.playTime = timedelta(seconds=0)
        self.settings = ProfileSettings(None, None)
        self.comment = None
        self.groupName = '' # 48 bytes?
        self.lastPointsDiff = 0
        self.lastRatingDiff = 0

class ProfileSettings:
    
    def __init__(self, settings1, settings2):
        self.settings1 = settings1
        self.settings2 = settings2


class UserInfo:

    def __init__(self, gameName, rosterHash):
        self.gameName = gameName
        self.rosterHash = rosterHash


class User:
    
    def __init__(self, hash):
        self.hash = hash
        self.configElement = None
        self.profiles = []
        self.lobbyOrdinal = None
        self.lobbyConnection = None
        self.gameVersion = None
        self.room = None
        self.nonce = None
        self.state = None
        self.needsLobbyChatReplay = False

    def __str__(self):
        res = ("(hash=%s)" % self.hash)
        res += ("(profiles=%s)" % ','.join([x.name for x in self.profiles]))
        return res

    def sendData(self, packetId, data):
        if self.lobbyConnection is None:
            log.debug('WARN: Cannot send data to user {%s}: no lobby connection' % self.hash)
        else:
            self.lobbyConnection.sendData(packetId, data)

    def getProfileById(self, profileId):
        for i, profile in enumerate(self.profiles):
            if profile.id == profileId:
                return i, profile
        return -1, None
            
    def getRoomId(self):
        try: return self.state.room.id
        except AttributeError:
            return 0


class UserState:
    """
    Encapsulate current state of the user:
    IP-addresses, ports, lobby Id, etc.
    """

    def tostr(self, s):
        return util.stripZeros(str(s))

    def __str__(self):
        return 'UserState: (%s)' % ','.join(["%s=%s" % (k,self.tostr(v)) 
                for k,v in self.__dict__.iteritems()])


class Stats:
    """
    Holder object of various stats for a user profile:
    wins, losses, draws, goals, etc.
    """

    def __init__(self, profile_id, wins, losses, draws, histWins, histLosses, histDraws, histDC,
                 goals_scored, goals_allowed,
                 streak_current, streak_best, teams=None):
        self.profile_id = profile_id
        self.wins = wins
        self.losses = losses
        self.draws = draws
        self.histWins = histWins
        self.histLosses = histLosses
        self.histDraws = histDraws
        self.histDC = histDC
        self.goals_scored = goals_scored
        self.goals_allowed = goals_allowed
        self.streak_current = streak_current
        self.streak_best = streak_best
        if not teams:
            self.teams = []
        else:
            self.teams = teams

