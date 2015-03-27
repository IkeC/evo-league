import math


class RatingMath:
    """
    Utility class used to calculate user points, based
    on the user's "performance". The performance is currently
    computed as non-loosing percentage, with a draw
    being worth 1/3 of a win.


    For weights 0.44, 0.56, getPoints behaves like this:
    ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
    vert: peformance, horiz: number of games

        |   5  10  50 100 200 1000
    ----+-------------------------
    0.00| 123 220 514 556 559 560
    0.10| 128 224 518 560 564 564
    0.20| 141 237 531 573 577 577
    0.30| 163 259 553 595 599 599
    0.40| 194 290 584 626 630 630
    0.50| 233 330 624 666 669 670
    0.60| 282 378 672 714 718 718
    0.70| 339 435 729 771 775 775
    0.80| 405 501 795 837 841 841
    0.90| 480 576 870 912 916 916
    1.00| 563 660 954 996 999 1000
    """

    def __init__(self, w1, w2):
        """
        w1 and w2 must be normalized weights
        (meaning: w1+w2 = 1.0)
        """
        self.w1 = w1
        self.w2 = w2

    def getScore(self, perf, num_games):
        return perf

    def getPoints(self, stats, dc):
        num_games = stats.wins + stats.draws + stats.losses + dc
        multiplier = 0
        if num_games == 0:
            perf = 0.0
        else:
            perf = (stats.wins + 0.5*stats.draws)/num_games
            if num_games <= 20:
                multiplier = num_games/float(50)+0.5
            elif num_games < 100:
                multiplier = 0.9+(float(0.1)*(num_games-20)/float(80))
            else:
                multiplier = 1
        return int(1000*perf*multiplier)

    def getRating(self, stats, dc):
        num_games = int(stats.wins + stats.draws + stats.losses + dc + stats.histWins + stats.histDraws + stats.histLosses + stats.histDC)
        multiplier = 0
        if num_games == 0:
            perf = 0.0
        else:
            perf = (stats.wins + int(stats.histWins) + 0.5*int(stats.draws) + 0.5*int(stats.histDraws))/num_games
            if num_games <= 20:
                multiplier = num_games/float(50)+0.5
            elif num_games < 100:
                multiplier = 0.9+(float(0.1)*(num_games-20)/float(80))
            else:
                multiplier = 1
        return int(1000*perf*multiplier)

    def getDivision(self, points):
        """
        Calculate division, based on number of points
        """
        for division, threshold in enumerate([250,450,600,750]):
            if points < threshold:
                return division
        return 4

