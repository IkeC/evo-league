"""
Data-layer for PES6
"""

from twisted.internet import defer
from datetime import timedelta
from model import user
import time
import data
import log
import sys
import binascii
import traceback

class UserData(data.UserData):
    
    def __init__(self, dbController):
        self.dbController = dbController

    @defer.inlineCallbacks
    def get(self, id):
        #sql = ('SELECT id,username,serial,hash,reset_nonce,updated_on '
        #       'FROM users WHERE deleted = 0 AND id = %s')
        sql = ('SELECT player_id,name,serial6,hash6 '
                             'FROM weblm_players WHERE approved ="yes" AND id = %s')
        rows = yield self.dbController.dbRead(0, sql, id)
        results = []
        for row in rows:
            usr = user.User(row[3])
            usr.id = row[0]
            usr.username = row[1]
            usr.serial = row[2]
            usr.hash = row[3]
            usr.nonce = None
            #usr.updatedOn = row[5]
            results.append(usr)
        defer.returnValue(results)

    @defer.inlineCallbacks
    def browse(self, offset=0, limit=30):
        sql = ('SELECT count(id) '
               'FROM  weblm_players WHERE approved="yes"')
        rows = yield self.dbController.dbRead(0, sql)
        total = int(rows[0][0])
        sql = ('SELECT player_id,name,serial6,hash6 '
               'FROM weblm_players WHERE approved="yes" '
               'ORDER BY name LIMIT %s OFFSET %s')
        rows = yield self.dbController.dbRead(0, sql, limit, offset)
        results = []
        for row in rows:
            usr = user.User(row[3])
            usr.id = row[0]
            usr.username = row[1]
            usr.serial = row[2]
            usr.hash = row[3]
            usr.nonce = None
            #usr.updatedOn = row[5]
            results.append(usr)
        defer.returnValue((total, results))
        
    @defer.inlineCallbacks
    def store(self, usr):
        sql = ('INSERT INTO weblm_players (player_id,name,serial6) '
               'VALUES (%s,%s,%s) ON DUPLICATE KEY UPDATE '
               'deleted=0, username=%s, serial=%s, hash=%s, reset_nonce=%s')
        params = (usr.id, usr.username, usr.serial, usr.hash,
                  usr.nonce, usr.username, usr.serial, usr.hash,
                  usr.nonce)
        defer.returnValue(True)
        
    @defer.inlineCallbacks
    def delete(self, usr):
        sql = 'UPDATE users SET deleted = 1 WHERE id = %s'
        params = (usr.id,)
        defer.returnValue(True)

    @defer.inlineCallbacks
    def findByUsername(self, username):
        sql = ('SELECT player_id,name,serial6,hash6 '
               'FROM weblm_players WHERE approved="yes" AND name = %s')
        rows = yield self.dbController.dbRead(0, sql, username)
        results = []
        for row in rows:
            usr = user.User(row[3])
            usr.id = row[0]
            usr.username = row[1]
            usr.serial = row[2]
            usr.hash = row[3]
            usr.nonce = None
            #usr.updatedOn = row[5]
            results.append(usr)
        defer.returnValue(results)

    @defer.inlineCallbacks
    def findByHash(self, hash):
        #log.debug('EVO: UserData.findByHash: %s' % hash)
        sql = ('SELECT player_id,name,serial6,hash6 '
               'FROM weblm_players WHERE approved="yes" AND hash6 = %s')
        rows = yield self.dbController.dbRead(0, sql, hash)
        results = []
        for row in rows:
            usr = user.User(row[3])
            usr.id = row[0]
            usr.username = row[1]
            usr.serial = row[2]
            usr.hash = row[3]
            usr.nonce = None
            #usr.updatedOn = row[5]
            results.append(usr)
        defer.returnValue(results)
    
    @defer.inlineCallbacks
    def getUserIdForProfileId(self, profileId):
        log.debug('getUserIdForProfileId: profileId=%s' % profileId)
        sql = ('SELECT user_id FROM six_profiles WHERE id=%s')
        rows = yield self.dbController.dbRead(0, sql, profileId)
        for row in rows:
            userId = row[0]
        log.debug('getUserIdForProfileId: userId=%s' % userId)
        defer.returnValue(userId)
        
    @defer.inlineCallbacks
    def findByNonce(self, nonce):        
        log.debug('EVO: called UserData.findByNonce')
        sql = ('SELECT id,username,serial,hash,reset_nonce,updated_on '
               'FROM users WHERE deleted = 0 AND reset_nonce = %s')
        #rows = yield self.dbController.dbRead(0, sql, nonce)
        results = []
        #for row in rows:
        #    usr = user.User(row[3])
        #    usr.id = row[0]
        #    usr.username = row[1]
        #    usr.serial = row[2]
        #    usr.hash = row[3]
        #    usr.nonce = row[4]
        #    #usr.updatedOn = row[5]
        #    results.append(usr)
        defer.returnValue(results)


class ProfileData(data.ProfileData):
    """
    Not quite the same as PES ProfileData, because
    of new fields: rating, comment
    """

    def __init__(self, dbController):
        self.dbController = dbController

    @defer.inlineCallbacks
    def get(self, id):
        sql = ('SELECT sp.id, sp.user_id, sp.ordinal, sp.name, sp.rank,'
               'sp.rating, sp.points, sp.disconnects, sp.updated_on, sp.seconds_played, sp.comment, '
               'wp.forum as groupName '
               'FROM six_profiles sp '
               'LEFT JOIN weblm_players wp ON sp.user_id=wp.player_id '
               'WHERE sp.deleted = 0 AND sp.id = %s')
        rows = yield self.dbController.dbRead(0, sql, id)
        results = []
        for row in rows:
            (id, userId, ordinal, name, rank, rating, 
             points, disconnects, updatedOn, secondsPlayed, comment, groupName) = row
            playTime = timedelta(seconds=secondsPlayed)
            p = user.Profile(ordinal)
            p.id = id
            p.userId = userId
            p.name = name
            p.rank = rank
            p.rating = rating
            p.points = points
            p.disconnects = disconnects
            p.updatedOn = updatedOn
            p.playTime = playTime
            p.comment = comment
            p.groupName = groupName
            results.append(p)
        defer.returnValue(results)

    @defer.inlineCallbacks
    def getByUserId(self, userId):
        sql = ('SELECT sp.id, sp.user_id, sp.ordinal, sp.name, sp.rank,'
               'sp.rating, sp.points, sp.disconnects, sp.updated_on, sp.seconds_played, sp.comment, '
               'wp.forum as groupName '
               'FROM six_profiles sp '
               'LEFT JOIN weblm_players wp ON sp.user_id=wp.player_id '
               'WHERE sp.deleted = 0 AND sp.user_id = %s '
               'ORDER BY updated_on ASC')
        rows = yield self.dbController.dbRead(0, sql, userId)
        results = []
        for row in rows:
            (id, userId, ordinal, name, rank, rating, 
             points, disconnects, updatedOn, secondsPlayed, comment, groupName) = row
            playTime = timedelta(seconds=secondsPlayed)
            p = user.Profile(ordinal)
            p.id = id
            p.userId = userId
            p.name = name
            p.rank = rank
            p.rating = rating
            p.points = points
            p.disconnects = disconnects
            p.updatedOn = updatedOn
            p.playTime = playTime
            p.comment = comment
            p.groupName = groupName
            results.append(p)
        defer.returnValue(results)

    @defer.inlineCallbacks
    def browse(self, offset=0, limit=30):
        sql = ('SELECT count(id) '
               'FROM six_profiles WHERE deleted = 0')
        rows = yield self.dbController.dbRead(0, sql)
        total = int(rows[0][0])
        sql = ('SELECT sp.id, sp.user_id, sp.ordinal, sp.name, sp.rank,'
               'sp.rating, sp.points, sp.disconnects, sp.updated_on, sp.seconds_played, sp.comment, '
               'wp.forum as groupName '
               'FROM six_profiles sp '
               'LEFT JOIN weblm_players wp ON sp.user_id=wp.player_id '
               'WHERE sp.deleted = 0 '
               'ORDER BY sp.name LIMIT %s OFFSET %s')
        rows = yield self.dbController.dbRead(0, sql, limit, offset)
        results = []
        for row in rows:
            (id, userId, ordinal, name, rank, rating, 
             points, disconnects, updatedOn, secondsPlayed, comment, groupName) = row
            playTime = timedelta(seconds=secondsPlayed)
            p = user.Profile(ordinal)
            p.id = id
            p.userId = userId
            p.name = name
            p.rank = rank
            p.rating = rating
            p.points = points
            p.disconnects = disconnects
            p.updatedOn = updatedOn
            p.playTime = playTime
            p.comment = comment
            p.groupName = groupName
            results.append(p)
        defer.returnValue((total, results))

    @defer.inlineCallbacks
    def store(self, p):
        sql = ('INSERT INTO six_profiles (id,user_id,ordinal,name,'
               'rank,rating,points,disconnects,seconds_played,comment) '
               'VALUES (%s,%s,%s,%s,%s,%s,%s,%s,%s,%s) '
               'ON DUPLICATE KEY UPDATE '
               'deleted=0, user_id=%s, ordinal=%s, name=%s, '
               'rank=%s, rating=%s, points=%s, '
               'disconnects=%s, seconds_played=%s, comment=%s')
        params = (p.id, p.userId, p.index, p.name, 
                  p.rank, p.rating, p.points, p.disconnects, p.playTime.seconds,
                  p.comment, p.userId, p.index, p.name, p.rank,
                  p.rating, p.points, p.disconnects, p.playTime.seconds, 
                  p.comment)
        yield self.dbController.dbWrite(0, sql, *params)
        defer.returnValue(True)

    @defer.inlineCallbacks
    def findByName(self, profileName):
        sql = ('SELECT sp.id, sp.user_id, sp.ordinal, sp.name, sp.rank,'
               'sp.rating, sp.points, sp.disconnects, sp.updated_on, sp.seconds_played, sp.comment, '
               'wp.forum as groupName '
               'FROM six_profiles sp '
               'LEFT JOIN weblm_players wp ON sp.user_id=wp.player_id '
               'WHERE sp.deleted = 0 AND sp.name = %s')
        rows = yield self.dbController.dbRead(0, sql, profileName)
        results = []
        for row in rows:
            (id, userId, ordinal, name, rank, rating, 
             points, disconnects, updatedOn, secondsPlayed, comment, groupName) = row
            playTime = timedelta(seconds=secondsPlayed)
            p = user.Profile(ordinal)
            p.id = id
            p.userId = userId
            p.name = name
            p.rank = rank
            p.rating = rating
            p.points = points
            p.disconnects = disconnects
            p.updatedOn = updatedOn
            p.playTime = playTime
            p.comment = comment
            p.groupName = groupName
            results.append(p)
        defer.returnValue(results)
    
    @defer.inlineCallbacks
    def getSettings(self, profileId):
        sql = ('SELECT settings1, settings2 '
               'FROM six_settings WHERE profile_id=%s')
        rows = yield self.dbController.dbRead(0, sql, profileId)
        if len(rows)>0:
            settings = user.ProfileSettings(rows[0][0], rows[0][1])
        else:
            settings = user.ProfileSettings(None, None)
        defer.returnValue(settings)

    @defer.inlineCallbacks
    def storeSettings(self, profileId, settings):
        sql = ('INSERT INTO six_settings (profile_id, settings1, settings2) '
               'VALUES (%s, %s, %s) '
               'ON DUPLICATE KEY UPDATE settings1=%s, settings2=%s')
        yield self.dbController.dbWrite(
            0, sql, profileId, settings.settings1, settings.settings2,
            settings.settings1, settings.settings2)
        defer.returnValue(settings)

    @defer.inlineCallbacks
    def delete(self, p):
        sql = 'UPDATE six_profiles SET deleted = 1 WHERE id = %s'
        params = (p.id,)
        yield self.dbController.dbWrite(0, sql, *params)
        defer.returnValue(True)


    @defer.inlineCallbacks
    def computeRanks(self):
        result = yield self.dbController.dbWriteInteraction(
            0, self._computeRanksTxn)
        defer.returnValue(result)

    def _computeRanksTxn(self, transaction):
        rank, count, rank_range = 1, 1, 100
        last_points = None
        limit, offset = 50, 0
        while True:
            sql = ('SELECT sp.id, sp.points FROM six_profiles sp '
                   'LEFT JOIN weblm_players wp ON wp.player_id=sp.user_id '
                   'WHERE wp.approved="yes" '
                   'ORDER BY sp.points DESC, wp.name ASC '
                   'LIMIT %s OFFSET %s')
            params = [limit, offset]
            transaction.execute(sql, params)
            rows = transaction.fetchall()
            for (id, points) in rows:
                if last_points is not None:
                    # check if rank needs to be lowered
                    if last_points > points:
                        rank = count
                sql = ('UPDATE six_profiles SET rank=%s WHERE id=%s')
                params = [rank, id]
                transaction.execute(sql, params)
                last_points = points
                count += 1
            if len(rows) < limit:
                break
            offset += limit

class MatchData:

    def __init__(self, dbController):
        self.dbController = dbController

    @defer.inlineCallbacks
    def getGames(self, profileId):
        sql = ('SELECT count(smp.id) FROM six_matches_played smp '
               'LEFT JOIN six_matches sm ON sm.id=smp.match_id '
               'WHERE smp.profile_id=%s '
               'AND sm.numParticipants=2 '
               'AND sm.season=(SELECT season FROM six_stats)')
        rows = yield self.dbController.dbRead(0, sql, profileId)
        defer.returnValue(rows[0][0])

    @defer.inlineCallbacks
    def getWins(self, profileId):
        sql = ('SELECT count(six_matches.id) FROM six_matches, six_matches_played '
               'WHERE six_matches.id=six_matches_played.match_id '
               'AND profile_id=%s '
               'AND ((home=1 and score_home>score_away) OR (home=0 and score_home<score_away)) '
               'AND six_matches.numParticipants=2 '
               'AND six_matches.season=(SELECT season FROM six_stats)')
        rows = yield self.dbController.dbRead(0, sql, profileId)
        defer.returnValue(rows[0][0])

    @defer.inlineCallbacks
    def getLosses(self, profileId):
        sql = ('SELECT count(six_matches.id) FROM six_matches, six_matches_played '
               'WHERE six_matches.id=six_matches_played.match_id '
               'AND profile_id=%s '
               'AND six_matches.numParticipants=2 '
               'AND ((home=1 and score_home<score_away) OR (home=0 and score_home>score_away)) '
               'AND six_matches.season=(SELECT season FROM six_stats)')
        rows = yield self.dbController.dbRead(0, sql, profileId)
        defer.returnValue(rows[0][0])

    @defer.inlineCallbacks
    def getDraws(self, profileId):
        sql = ('SELECT count(six_matches.id) FROM six_matches, six_matches_played '
               'WHERE six_matches.id=six_matches_played.match_id '
               'AND profile_id=%s '
               'AND score_home=score_away '
               'AND six_matches.numParticipants=2 '
               'AND six_matches.season=(SELECT season FROM six_stats)')
        rows = yield self.dbController.dbRead(0, sql, profileId)
        defer.returnValue(rows[0][0])

    @defer.inlineCallbacks
    def getGoalsHome(self, profileId):
        sql = ('SELECT sum(score_home),sum(score_away) '
               'FROM six_matches, six_matches_played '
               'WHERE six_matches.id=six_matches_played.match_id '
               'AND profile_id=%s AND home=1 '
               'AND six_matches.numParticipants=2 '
               'AND six_matches.season=(SELECT season FROM six_stats)')
        rows = yield self.dbController.dbRead(0, sql, profileId)
        scored = rows[0][0] or 0
        allowed = rows[0][1] or 0
        defer.returnValue((int(scored), int(allowed)))

    @defer.inlineCallbacks
    def getGoalsAway(self, profileId):
        sql = ('SELECT sum(score_away),sum(score_home) '
               'FROM six_matches, six_matches_played '
               'WHERE six_matches.id=six_matches_played.match_id '
               'AND profile_id=%s AND home=0 '
               'AND six_matches.numParticipants=2 '
               'AND six_matches.season=(SELECT season FROM six_stats)')
        rows = yield self.dbController.dbRead(0, sql, profileId)
        scored = rows[0][0] or 0
        allowed = rows[0][1] or 0
        defer.returnValue((int(scored), int(allowed)))

    @defer.inlineCallbacks
    def getHistoryData(self, profileId):
        sql = ('SELECT sum(wins), sum(losses), sum(draws), sum(DC) '
               'FROM six_history '
               'WHERE profileId=%s')
        rows = yield self.dbController.dbRead(0, sql, profileId)
        wins = rows[0][0] or 0
        losses = rows[0][1] or 0
        draws = rows[0][2] or 0
        DC = rows[0][3] or 0
        defer.returnValue((wins, losses, draws, DC))

    @defer.inlineCallbacks
    def getStreaks(self, profileId):
        sql = ('SELECT wins, best FROM six_streaks '
               'WHERE profile_id=%s')
        rows = yield self.dbController.dbRead(0, sql, profileId)
        wins, best = 0, 0
        if len(rows)>0:
            wins, best = rows[0][0], rows[0][1]
        defer.returnValue((wins, best))

    @defer.inlineCallbacks
    def getLastTeamsUsed(self, profileId, numMatches):
        sql = ('SELECT match_id, team_id_home, team_id_away, home '
               'FROM six_matches_played, six_matches '
               'WHERE profile_id=%s AND six_matches.id=match_id '
               'ORDER BY match_id DESC LIMIT %s')
        args = (profileId, numMatches,)
        rows = yield self.dbController.dbRead(0, sql, *args)
        teams = []
        for row in rows:
            match_id, team_id_home, team_id_away, home = row
            if home:
                teams.append(team_id_home)
            else:
                teams.append(team_id_away)
        defer.returnValue(teams)

    @defer.inlineCallbacks
    def store(self, match, hashHome, hashAway, lobbyName, roomName, season):
        matchId = yield self.dbController.dbWriteInteraction(
            0, self._storeTxn, match, hashHome, hashAway, lobbyName, roomName, season)
        defer.returnValue(matchId)

    def _storeTxn(self, transaction, match, hashHome, hashAway, lobbyName, roomName, season):
        def _writeStreak(profile_id, win):
            wins, best = 0, 0
            sql = ('SELECT wins, best FROM six_streaks '
                   'WHERE profile_id=%s')
            transaction.execute(sql, (profile_id,))
            data = transaction.fetchall()
            if len(data)>0:
                wins, best = data[0][0], data[0][1]
            if win:
                wins += 1
                best = max(wins, best)
            else:
                wins = 0
            sql = ('INSERT INTO six_streaks (profile_id, wins, best) '
                   'VALUES (%s,%s,%s) ON DUPLICATE KEY UPDATE '
                   'wins=%s, best=%s')
            log.msg('data6.py: _writeStreak: profile_id=%s wins=%s, best=%s' % (profile_id, wins, best))
            transaction.execute(sql, (profile_id, wins, best, wins, best))

        # record match result        
        home_players = [match.teamSelection.home_captain]
        home_players.extend(match.teamSelection.home_more_players)
        away_players = [match.teamSelection.away_captain]
        away_players.extend(match.teamSelection.away_more_players)
        
        numParticipants = len(home_players) + len(away_players)
        
        log.msg('data6.py: MatchData: _storeTxn: numParticipants=%s' % numParticipants)
        
        sql = ('INSERT INTO six_matches '
               '(score_home, score_away, score_home_reg, score_away_reg, team_id_home, team_id_away, hashHome, hashAway, lobbyName, roomName, minutes, season, numParticipants) '
               'VALUES (%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s)')
        transaction.execute(sql, ( 
            match.score_home, match.score_away, match.score_home_reg, match.score_away_reg, 
            match.teamSelection.home_team_id, match.teamSelection.away_team_id, 
            hashHome, hashAway, lobbyName, roomName, match.clock, season, numParticipants))
        transaction.execute('SELECT LAST_INSERT_ID()')
        matchId = transaction.fetchall()[0][0]

        # record players of the match
        for profile in home_players:
            sql = ('INSERT INTO six_matches_played (match_id, profile_id, home, pointsDiff, ratingDiff) '
                   'VALUES (%s, %s, 1, %s, %s)')
            transaction.execute(sql, (matchId, profile.id, 0, 0))
        for profile in away_players:
            sql = ('INSERT INTO six_matches_played (match_id, profile_id, home, pointsDiff, ratingDiff) '
                   'VALUES (%s, %s, 0, %s, %s)')
            transaction.execute(sql, (matchId, profile.id, 0, 0))
        
        # update winning streaks (1on1)
        if numParticipants == 2:
            if match.score_home > match.score_away:
                # home win
                for profile in home_players:
                    _writeStreak(profile.id, True)
                for profile in away_players:
                    _writeStreak(profile.id, False)
            elif match.score_home < match.score_away:
                # away win
                for profile in home_players:
                    _writeStreak(profile.id, False)
                for profile in away_players:
                    _writeStreak(profile.id, True)
            else:
                # draw
                for profile in home_players:
                    _writeStreak(profile.id, False)
                for profile in away_players:
                    _writeStreak(profile.id, False)
        return matchId

    @defer.inlineCallbacks
    def MatchStatusInsert(self, state, profileHome, profileHome2, profileHome3, profileAway, profileAway2, profileAway3, hashHome, hashAway, teamHome, teamAway, lobbyName, season):
        matchId = -1
        try:
            sql = ('INSERT INTO six_matches_status '
                   '(minutes, state, profileHome, profileHome2, profileHome3, profileAway, profileAway2, profileAway3, scoreHome, scoreAway, scoreHomeReg, scoreAwayReg, hashHome, hashAway, teamHome, teamAway, lobbyName, season) '
                   'VALUES (%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s)')
            log.msg("MatchStatusInsert: [%s] [%s] [%s] [%s] [%s] [%s] [%s] [%s] [%s] [%s] [%s] [%s] [%s]" % (state, profileHome, profileHome2, profileHome3, profileAway, profileAway2, profileAway3, hashHome, hashAway, teamHome, teamAway, lobbyName, season))
            self.dbController.dbWrite(0, sql, 0, state, profileHome, profileHome2, profileHome3, profileAway, profileAway2, profileAway3, 0, 0, 0, 0, hashHome, hashAway, teamHome, teamAway, lobbyName, season)  
            
            sql = ('SELECT id FROM six_matches_status '
                   'WHERE profileHome=%s AND profileHome2=%s AND profileAway=%s AND profileAway2=%s '
                   'AND updated >= date_sub(now(), INTERVAL 1 MINUTE) '
                   'ORDER BY id DESC LIMIT 1')
            rows = yield self.dbController.dbRead(0, sql, profileHome, profileHome2, profileAway, profileAway2)
            if rows is not None:
                if len(rows)>0:
                    matchId = int(rows[0][0])
                    log.msg("MatchStatusInsert: matchId=%s" % matchId)
        except:
            print('MatchStatusInsert exception')
            log.msg("Error inserting into six_matches_status: %s" % sys.exc_info()[0])
            exc_type, exc_value, exc_traceback = sys.exc_info()
            lines = traceback.format_exception(exc_type, exc_value, exc_traceback)
            log.msg("Lines: %s" % lines)
        defer.returnValue(matchId)

    def MatchStatusUpdate(self, minutes, state, scoreHome, scoreAway, scoreHomeReg, scoreAwayReg, matchId):
        try:
            sql = ('UPDATE six_matches_status '
                   'SET minutes=%s, state=%s, scoreHome=%s, scoreAway=%s, scoreHomeReg=%s, scoreAwayReg=%s '
                   'WHERE id=%s')
            log.msg("MatchStatusUpdate: [%s] [%s] [%s] [%s] [%s] [%s] [%s]" % (minutes, state, scoreHome, scoreAway, scoreHomeReg, scoreAwayReg, matchId))
            self.dbController.dbWrite(0, sql, minutes, state, scoreHome, scoreAway, scoreHomeReg, scoreAwayReg, matchId)
        except:
            log.msg("Error updating six_matches_status: %s" % sys.exc_info()[0])
            exc_type, exc_value, exc_traceback = sys.exc_info()
            lines = traceback.format_exception(exc_type, exc_value, exc_traceback)
            log.msg("Lines: %s" % lines)

    def MatchStatusUpdateGoal(self, scoreHome, scoreAway, scoreHomeReg, scoreAwayReg, matchId):
        try:
            sql = ('UPDATE six_matches_status '
                   'SET minutes=minutes+1, scoreHome=%s, scoreAway=%s, scoreHomeReg=%s, scoreAwayReg=%s '
                   'WHERE id=%s')
            log.msg("MatchStatusUpdateGoal: [%s] [%s] [%s] [%s] [%s]" % (scoreHome, scoreAway, scoreHomeReg, scoreAwayReg, matchId))
            self.dbController.dbWrite(0, sql, scoreHome, scoreAway, scoreHomeReg, scoreAwayReg, matchId)
        except:
            log.msg("Error updating six_matches_status: %s" % sys.exc_info()[0])
            exc_type, exc_value, exc_traceback = sys.exc_info()
            lines = traceback.format_exception(exc_type, exc_value, exc_traceback)
            log.msg("Lines: %s" % lines)
            
    def MatchStatusDelete(self, matchId):
        try:
            sql = ('DELETE FROM six_matches_status WHERE id=%s')
            log.msg("MatchStatusDelete: [%s]" % matchId)
            self.dbController.dbWrite(0, sql, matchId)
            sql = ('DELETE FROM six_matches_info WHERE matchId=%s and type="U"')
            self.dbController.dbWrite(0, sql, matchId)
        except:
            log.msg("Error deleting from six_matches_status: %s" % sys.exc_info()[0])
            exc_type, exc_value, exc_traceback = sys.exc_info()
            lines = traceback.format_exception(exc_type, exc_value, exc_traceback)
            log.msg("Lines: %s" % lines)
    
    def MatchSetAdditionalInfo(self, matchId, type, matchSettings, matchStart, duration):
        try:
            sql = ('INSERT INTO six_matches_info '
                   '(matchId, type, matchStart, duration, matchTime, timeLimit, numberOfPauses, '
                   'conditionSetting, injuries, maxNoOfSubstitutions, '
                   'matchTypeEx, matchTypePk, timeSetting, season, weather) '                   
                   'VALUES (%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s,%s)')
            log.debug("MatchSetAdditionalInfo: SQL: %s" % sql)
            self.dbController.dbWrite(0, sql, 
              matchId, 
              type,
              matchStart, 
              duration,
              binascii.b2a_hex(matchSettings.match_time),
              binascii.b2a_hex(matchSettings.time_limit),
              binascii.b2a_hex(matchSettings.number_of_pauses),
              binascii.b2a_hex(matchSettings.condition),
              binascii.b2a_hex(matchSettings.injuries),
              binascii.b2a_hex(matchSettings.max_no_of_substitutions),
              binascii.b2a_hex(matchSettings.match_type_ex),
              binascii.b2a_hex(matchSettings.match_type_pk),
              binascii.b2a_hex(matchSettings.time),
              binascii.b2a_hex(matchSettings.season),
              binascii.b2a_hex(matchSettings.weather))
        except:
            log.msg("Error inserting into six_matches_info: %s" % sys.exc_info()[0])
            exc_type, exc_value, exc_traceback = sys.exc_info()
            lines = traceback.format_exception(exc_type, exc_value, exc_traceback)
            log.msg("Lines: %s" % lines)

    def MatchStatusSetHomeExit(self, matchId):
        try:
            sql = ('UPDATE six_matches_status SET updated=updated, homeExit=NOW() WHERE id=%s')
            self.dbController.dbWrite(0, sql, matchId)
        except:
            log.msg("Error in MatchStatusSetHomeExit: %s" % sys.exc_info()[0])
            exc_type, exc_value, exc_traceback = sys.exc_info()
            lines = traceback.format_exception(exc_type, exc_value, exc_traceback)
            log.msg("Lines: %s" % lines)

    def MatchStatusSetAwayExit(self, matchId):
        try:
            sql = ('UPDATE six_matches_status SET updated=updated, awayExit=NOW() WHERE id=%s')
            self.dbController.dbWrite(0, sql, matchId)
        except:
            log.msg("Error in MatchStatusSetAwayExit: %s" % sys.exc_info()[0])
            exc_type, exc_value, exc_traceback = sys.exc_info()
            lines = traceback.format_exception(exc_type, exc_value, exc_traceback)
            log.msg("Lines: %s" % lines)
    
    def MatchStatusSetCancel(self, matchId, profileCancelId):
        try:
            log.msg('MatchStatusSetCancel: matchId=%s profileCancelId=%s' % (matchId, profileCancelId))
            sql = ('UPDATE six_matches_status SET updated=updated, homeCancel=NOW() WHERE id=%s AND (profileHome=%s OR profileHome2=%s OR profileHome3=%s)')
            self.dbController.dbWrite(0, sql, matchId, profileCancelId, profileCancelId, profileCancelId)
            sql = ('UPDATE six_matches_status SET updated=updated, awayCancel=NOW() WHERE id=%s AND (profileAway=%s OR profileAway2=%s OR profileAway3=%s)')
            self.dbController.dbWrite(0, sql, matchId, profileCancelId, profileCancelId, profileCancelId)
        except:
            log.msg("Error in MatchStatusSetCancel: %s" % sys.exc_info()[0])
            exc_type, exc_value, exc_traceback = sys.exc_info()
            lines = traceback.format_exception(exc_type, exc_value, exc_traceback)
            log.msg("Lines: %s" % lines)

    @defer.inlineCallbacks
    def GetMatchId(self, profileHome, profileHome2, profileAway, profileAway2):
        matchId = -1
        try:
            sql = ('SELECT id FROM six_matches_status ' 
                   'WHERE profileHome=%s AND profileHome2=%s AND profileAway=%s AND profileAway2=%s '
                   'AND updated > date_sub(now(), INTERVAL 10 MINUTE) '
                   'ORDER BY id DESC LIMIT 1')
            rows = yield self.dbController.dbRead(0, sql, profileHome, profileHome2, profileAway, profileAway2)
            matchId = int(rows[0][0])
        except:
            log.msg("Error in data6.GetMatchId: %s" % sys.exc_info()[0])
            exc_type, exc_value, exc_traceback = sys.exc_info()
            lines = traceback.format_exception(exc_type, exc_value, exc_traceback)
            log.msg("Lines: %s" % lines)
        defer.returnValue(matchId)
    
    def UpdateMatchPointsAndRating(self, matchId, profileId, points, pointsDiff, rating, ratingDiff):
        try:
            sql = ('UPDATE six_matches_played ' 
                   'SET points=%s, pointsDiff=%s, rating=%s, ratingDiff=%s '
                   'WHERE match_id=%s AND profile_id=%s')
            self.dbController.dbWrite(0, sql, points, pointsDiff, rating, ratingDiff, matchId, profileId)
        except:
            log.msg("Error in data6.UpdateMatchPointsAndRating: %s" % sys.exc_info()[0])
            exc_type, exc_value, exc_traceback = sys.exc_info()
            lines = traceback.format_exception(exc_type, exc_value, exc_traceback)
            log.msg("Lines: %s" % lines)      
    
    @defer.inlineCallbacks
    def CheckBanned(self, profileId):
        banned = 0
        try:
            sql = ('SELECT count(sp.id) '
                   'FROM six_profiles sp '
                   'LEFT JOIN weblm_players wp ON sp.user_id=wp.player_id '
                   'WHERE wp.approved="no" '
                   'AND sp.id=%s')
            rows = yield self.dbController.dbRead(0, sql, profileId)
            banned = int(rows[0][0])
        except:
            log.msg("Error in CheckBanned: %s" % sys.exc_info()[0])
            exc_type, exc_value, exc_traceback = sys.exc_info()
            lines = traceback.format_exception(exc_type, exc_value, exc_traceback)
            log.msg("Lines: %s" % lines)
        # return banned
        defer.returnValue(banned)

class StatsData(data.StatsData):
    
    def __init__(self, dbController):
        self.dbController = dbController
        
    @defer.inlineCallbacks
    def storeOnlineUsers(self, onlineUsers):
        try:
            sql = ('UPDATE six_stats SET onlineUsers=%s')
            yield self.dbController.dbWrite(0, sql, onlineUsers)
            log.debug('set six_stats.onlineUsers=%s' % onlineUsers)
        except:
            log.msg("Error in storeOnlineUsers: %s" % sys.exc_info()[0])
            exc_type, exc_value, exc_traceback = sys.exc_info()
            lines = traceback.format_exception(exc_type, exc_value, exc_traceback)
            log.msg("Lines: %s" % lines)

    @defer.inlineCallbacks
    def CheckMaintenance(self):
        maintenance = 0
        try:
            sql = ('SELECT maintenance FROM six_stats')
            rows = yield self.dbController.dbRead(0, sql)
            maintenance = int(rows[0][0])
            log.msg("CheckMaintenance: maintenance=%s" % maintenance)
        except:
            log.msg("Error in CheckMaintenance: %s" % sys.exc_info()[0])
            exc_type, exc_value, exc_traceback = sys.exc_info()
            lines = traceback.format_exception(exc_type, exc_value, exc_traceback)
            log.msg("Lines: %s" % lines)
        # return maintenance
        defer.returnValue(maintenance)

    @defer.inlineCallbacks
    def CheckDebugMode(self):
        debugMode = 0
        try:
            sql = ('SELECT debugMode FROM six_stats')
            rows = yield self.dbController.dbRead(0, sql)
            debugMode = int(rows[0][0])
            log.msg("CheckDebugMode: debugMode=%s" % debugMode)
        except:
            log.msg("Error in CheckDebugMode: %s" % sys.exc_info()[0])
            exc_type, exc_value, exc_traceback = sys.exc_info()
            lines = traceback.format_exception(exc_type, exc_value, exc_traceback)
            log.msg("Lines: %s" % lines)
        # return debugMode
        defer.returnValue(debugMode)

    @defer.inlineCallbacks
    def CheckSeason(self):
        season = 1
        try:
            sql = ('SELECT season FROM six_stats')
            rows = yield self.dbController.dbRead(0, sql)
            season = int(rows[0][0])
            log.msg("CheckSeason: season=%s" % season)
        except:
            log.msg("Error in CheckSeason: %s" % sys.exc_info()[0])
            exc_type, exc_value, exc_traceback = sys.exc_info()
            lines = traceback.format_exception(exc_type, exc_value, exc_traceback)
            log.msg("Lines: %s" % lines)
        # return season
        defer.returnValue(season)

    @defer.inlineCallbacks
    def GetInfoMessage(self, season):
        profileName = "?"
        name = "?"
        endDate = "?"
        try:
          sql = ('SELECT sp.name AS profileName, wp.name '
                 'FROM six_profiles sp '
                 'LEFT JOIN weblm_players wp ON sp.user_id = wp.player_id '
                 'ORDER BY sp.points DESC, wp.name ASC '
                 'LIMIT 0,1')
          rows = yield self.dbController.dbRead(0, sql)
          profileName = rows[0][0]
          name = rows[0][1]
          
          sql = ('SELECT enddate FROM six_seasons WHERE season=%s')
          rows = yield self.dbController.dbRead(0, sql, season)
          endDate = rows[0][0]
        except:
          log.msg("Error in CheckSeason: %s" % sys.exc_info()[0])
          exc_type, exc_value, exc_traceback = sys.exc_info()
          lines = traceback.format_exception(exc_type, exc_value, exc_traceback)
          log.msg("Lines: %s" % lines)
        defer.returnValue((profileName, name, endDate))
        
    @defer.inlineCallbacks
    def WriteAccessLogEntry(self, userName, ip, logType):
        try:
            accesstime = int(time.time())
            sql = ('INSERT INTO weblm_log_access (user, ip, accesstime, logType) '
                   'VALUES (%s,%s,%s,%s)')
            logTime = int(time.time())
            params = (userName, ip, accesstime, logType)
            yield self.dbController.dbWrite(0, sql, *params)
        except:
            log.msg("Error in WriteAccessLogEntry: %s" % sys.exc_info()[0])
            exc_type, exc_value, exc_traceback = sys.exc_info()
            lines = traceback.format_exception(exc_type, exc_value, exc_traceback)
            log.msg("Lines: %s" % lines)

