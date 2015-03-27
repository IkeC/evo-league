from twisted.internet import defer
from model import user
import log
import sys
import errors
import traceback

class ProfileLogic:
    """
    Various logic related to a user profile.
    """

    def __init__(self, matchData, profileData):
        self.matchData = matchData
        self.profileData = profileData

    @defer.inlineCallbacks
    def getFullProfileInfoByName(self, profileName):
        profiles = yield self.profileData.findByName(profileName)
        try: stats = yield self.getStats(profiles[0].id)
        except IndexError:
            raise errors.ProfileNotFoundError(
                'profile not found for name: "%s"' % profileName)
        defer.returnValue((profiles[0], stats))

    @defer.inlineCallbacks
    def getFullProfileInfoById(self, profileId):
        profiles = yield self.profileData.get(profileId)
        try: stats = yield self.getStats(profiles[0].id)
        except IndexError:
            raise errors.ProfileNotFoundError(
                'profile not found for id: %s' % profileId)
        defer.returnValue((profiles[0], stats))

    @defer.inlineCallbacks
    def getStats(self, profileId):
        # wins, losses, draws
        results = yield defer.DeferredList([
            self.matchData.getWins(profileId),
            self.matchData.getLosses(profileId),
            self.matchData.getDraws(profileId)])
        (_,wins),(_,losses),(_,draws) = results
        # goals
        results = yield defer.DeferredList([
            self.matchData.getGoalsHome(profileId),
            self.matchData.getGoalsAway(profileId)])
        (_, (scored_home, allowed_home)) = results[0]
        (_, (scored_away, allowed_away)) = results[1]
        goals_scored = scored_home + scored_away
        goals_allowed = allowed_home + allowed_away
        # historic data
        historyWins = 0
        historyLosses = 0
        historyDraws = 0
        historyDC = 0
        try:
            results = yield self.matchData.getHistoryData(profileId)
            historyWins, historyLosses, historyDraws, historyDC = results
        except:
            log.msg('ERROR in logic.getStats:')
            exc_type, exc_value, exc_traceback = sys.exc_info()
            lines = traceback.format_exception(exc_type, exc_value, exc_traceback)
            log.msg("Lines: %s" % lines)
        # log.msg("HistoryData: W=%s L=%s D=%s DC=%s" % (historyWins, historyLosses, historyDraws, historyDC))
        
        # streaks
        results = yield self.matchData.getStreaks(profileId)
        current, best = results
        # last 5 teams
        if hasattr(self.matchData, 'getLastTeamsUsed'):
            teams = yield self.matchData.getLastTeamsUsed(profileId, 5)
        else:
            teams = None

        stats = user.Stats(
            profileId, wins, losses, draws, 
            historyWins, historyLosses, historyDraws, historyDC,
            goals_scored, goals_allowed,
            current, best, teams)
        defer.returnValue(stats)

