"""
Protocol implementations for PES5/PES6 packet server.
"""

from twisted.internet.protocol import Protocol, ServerFactory
from twisted.protocols.policies import TimeoutMixin
from fiveserver.model import packet
from fiveserver.model.util import PacketFormatter
from fiveserver import log, stream, errors

import time
import sys
import traceback

def isSameGame(factory, userA, userB):
    aInfo = factory.getUserInfo(userA)
    bInfo = factory.getUserInfo(userB)
    result = (userA.gameVersion == userB.gameVersion and
        aInfo.gameName == bInfo.gameName)
    if not result:
        log.msg('INFO: Game versions differ: %s(%s,%s) != %s(%s,%s). ' % (
            userA.profile.name, userA.gameVersion, 
            aInfo.gameName,
            userB.profile.name, userB.gameVersion, 
            bInfo.gameName))
    return result


class PacketReceiver(Protocol, TimeoutMixin):
    """
    Base class for packet-receiving protocols
    """

    def packetReceived(self, pkt):
        """
        Override this.
        """
        raise errors.NotImplementedError

    def connectionMade(self):
        #print dir(self)
        self._recvd = ""
        self._count = 1

    def connectionLost(self, reason):
        log.debug('Connection lost: %s' % reason.getErrorMessage())
        if self.factory.serverConfig.Debug:
            try:
                log.debug('Traceback: %s' % reason.getTraceback())
            except:
                log.msg('Could not log traceback')

    def dataReceived(self, data):
        try:
            self.resetTimeout()
            self._recvd += data
            while len(self._recvd) >= 8:
                hdr = packet.makePacketHeader(stream.xorData(self._recvd[:8], 0))
                if len(self._recvd) < hdr.length + 24:
                    break
                pkt = packet.makePacket(
                    stream.xorData(self._recvd[:hdr.length + 24], 8))
                self._recvd = self._recvd[hdr.length + 24:]
                self._packetReceived(pkt)
        except:
            log.msg("ERROR in ___init___.py:PacketReceiver.dataReceived: %s" % sys.exc_info()[0])
            exc_type, exc_value, exc_traceback = sys.exc_info()
            lines = traceback.format_exception(exc_type, exc_value, exc_traceback)
            log.msg("Lines: %s" % lines)

    def timeoutConnection(self):
        log.msg("___init___.py: timeoutConnection")

    def send(self, pkt):
        #log.msg('sending: %s' % repr(pkt))
        if self.factory.serverConfig.Debug:
            try:
                username = self._user.profile.name
            except AttributeError:
                username = ''
            log.debug('[SEND {%s}]: %s' % (
                username, PacketFormatter.format(pkt)))
        self.transport.write(stream.xorData(str(pkt),0))
        self._count += 1

    def sleep(self, result, seconds):
        time.sleep(seconds)

    def _packetReceived(self, pkt):
        self.resetTimeout()
        try:
            if self.factory.serverConfig.Debug:
                try:
                    username = self._user.profile.name
                except AttributeError:
                    username = ''            
                log.debug('[RECV {%s}]: %s' % (
                    username, PacketFormatter.format(pkt)))
    
            # handle heartbeat packet here, since it's the same
            # across all types of servers
            if pkt.header.id == 0x0005:
                pkt.header.packet_count = self._count
                self.send(pkt)
                return
            # let subclasses handle it
            self.packetReceived(pkt)
        except:
            log.msg("Error in protocol._packetReceived: %s" % sys.exc_info()[0])
            exc_type, exc_value, exc_traceback = sys.exc_info()
            lines = traceback.format_exception(exc_type, exc_value, exc_traceback)
            log.msg("Lines: %s" % lines)
            log.msg('[RECV {%s}]: %s' % (username, PacketFormatter.format(pkt)))

    def sendZeros(self, id, length):
        self.sendData(id, '\0'*length)

    def sendData(self, id, data):
        self.send(
            packet.Packet(packet.PacketHeader(id,len(data),self._count),data))


class PacketServiceFactory(ServerFactory):
    """
    Factory class for all Fiveserver services
    """

    def __init__(self, configuration):
        self.configuration = configuration

    def __getattr__(self, name):
        return getattr(self.configuration, name)

    def buildProtocol(self, addr):
        p = ServerFactory.buildProtocol(self, addr)
        p.addr = addr
        return p

 
class PacketDispatcher(PacketReceiver):
    """
    Base class for dispatcher-type services.
    Packet ID is examined and corresponding handler method
    is called to take care of it.
    """

    def connectionMade(self):
        try:
            self.transport.setTcpKeepAlive(1)
        except:
            log.msg("Error in protocol.connectionMade: %s" % sys.exc_info()[0])
            exc_type, exc_value, exc_traceback = sys.exc_info()
            lines = traceback.format_exception(exc_type, exc_value, exc_traceback)
            log.msg("Lines: %s" % lines)
            log.msg('[RECV {%s}]: %s' % (username, PacketFormatter.format(pkt)))
        try:
            PacketReceiver.connectionMade(self)
            self._handlers = dict()
            self.register()
        except:
            log.msg("Error in protocol.connectionMade: %s" % sys.exc_info()[0])
            exc_type, exc_value, exc_traceback = sys.exc_info()
            lines = traceback.format_exception(exc_type, exc_value, exc_traceback)
            log.msg("Lines: %s" % lines)

    def addHandler(self, packet_id, handler):
        self._handlers[packet_id] = handler

    def register(self):
        """
        Override this. Child classes should
        register their unique handlers for various
        packets by calling addHandler method
        """
        raise errors.NotImplementedError 

    def packetReceived(self, pkt):
        handler = self._handlers.get(pkt.header.id)
        if handler is not None:
            return handler(pkt)
        return self.defaultHandler(pkt)

    def defaultHandler(self, pkt):
        """
        Override this if you want behaviour
        different from simply ignoring the packet
        """
        pass

