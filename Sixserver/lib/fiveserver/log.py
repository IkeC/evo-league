"""
Simple logger module to wrap twisted.python.log
and to provide additional levels of logging, i.e. debug
"""

from twisted.python import log
import sys
import traceback

_debug = False

def getDebug():
    return _debug

def setDebug(value):
    global _debug
    _debug = value
    #log.msg('SYSTEM: Debug is %s' % {True:'ON', False:'OFF'}.get(_debug))

def msg(message):
    try:
      log.msg(message)
    except:
      log.msg("ERROR: could not log string (method 1)")
      exc_type, exc_value, exc_traceback = sys.exc_info()
      lines = traceback.format_exception(exc_type, exc_value, exc_traceback)
      log.msg("Lines: %s" % lines)
      try:
        msg2 = message.encode('ascii', 'ignore')
        log.msg(msg2)
        # log.msg("alternative method 1 worked")
      except:
        log.msg("ERROR: could not log string (method 2)")
        exc_type, exc_value, exc_traceback = sys.exc_info()
        lines = traceback.format_exception(exc_type, exc_value, exc_traceback)
        log.msg("Lines: %s" % lines)
        try:
          msg2 = message.decode()
          log.msg(msg2.encode('ascii', 'ignore'))
          # log.msg("alternative method 2 worked")
        except:
          log.msg("ERROR: could not log string (method 3)")
          exc_type, exc_value, exc_traceback = sys.exc_info()
          lines = traceback.format_exception(exc_type, exc_value, exc_traceback)
          log.msg("Lines: %s" % lines)



def debug(message):
    if _debug:
      msg(message)