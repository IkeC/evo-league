import binascii
import getopt
import sys

from Crypto.Cipher import Blowfish
hash = sys.argv[1:]
hash = hash[0]         
cipherkey='27501fd04e6b82c831024dac5c6305221974deb9388a21901d576cbbe2f377ef23d75486010f37819afe6c321a0146d21544ec365bf7289a'
cipher = Blowfish.new(binascii.a2b_hex(cipherkey))
crypt = binascii.b2a_hex(cipher.encrypt(binascii.a2b_hex(hash)))
print crypt