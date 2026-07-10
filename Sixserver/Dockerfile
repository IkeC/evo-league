FROM python:2.7-slim AS builder

# 1. Patch to historical (archived) repositories
RUN echo "deb http://archive.debian.org/debian/ buster main" > /etc/apt/sources.list && \
    echo "deb http://archive.debian.org/debian-security/ buster/updates main" >> /etc/apt/sources.list && \
    echo "Acquire::Check-Valid-Until \"false\";" > /etc/apt/apt.conf.d/10no--check-valid-until

# We need make, a C compiler (gcc/build-essential) and the Python headers
# (python2.7-dev) so that Twisted's native extensions compile cleanly.
# These tools only exist in this "builder" stage and do NOT travel to the final image.
RUN apt-get update && apt-get install -y make build-essential python2.7-dev && rm -rf /var/lib/apt/lists/*

RUN pip install "virtualenv<16.0.0"

# service.sh expects EVERYTHING under /opt/sixserver (sixserver-env, lib, etc/sixserver.tac),
# so we use that same path as the project root instead of /app.
WORKDIR /opt/sixserver
COPY . /opt/sixserver

# 2. HACK: Remove MySQL-python, pyOpenSSL, Twisted and zope.interface from the
# original file because we install them manually in later steps with compatible
# versions; keeping the original line (e.g. zope.interface==3.8.0, from 2011) would
# make "make install" try to reinstall an old version on top and likely fail.
RUN sed -i '/MySQL-python/d' ./pip.requirements && \
    sed -i '/pyOpenSSL/d' ./pip.requirements && \
    sed -i '/Twisted/d' ./pip.requirements && \
    sed -i '/zope.interface/d' ./pip.requirements

# 3. Force the virtualenv to be created at the path service.sh expects (FSENV)
RUN virtualenv /opt/sixserver/sixserver-env

# 4. MANUAL INSTALL
# --without-extensions does NOT exist as a real option in the setup.py of
# Twisted 10.2.0 nor in zope.interface's (that's why it failed with "option not
# recognized"). Since we already have gcc and the Python headers installed,
# we let it compile its C extensions normally.
RUN /opt/sixserver/sixserver-env/bin/pip install "pymysql<1.0" "pyOpenSSL==17.5.0"
RUN /opt/sixserver/sixserver-env/bin/pip install "zope.interface"
RUN /opt/sixserver/sixserver-env/bin/pip install "Twisted==10.2.0" --no-deps

# 5. Dynamically patch to use PyMySQL emulating MySQLdb
# Confirmed: the only file that uses MySQLdb is ./lib/fiveserver/storagecontroller.py
# It also does a separate "import _mysql_exceptions" (shipped with MySQL-python);
# we alias it to pymysql.err, which defines the same DB-API 2.0 exceptions.
RUN sed -i "1i import sys\nimport pymysql\nimport pymysql.err\npymysql.install_as_MySQLdb()\nsys.modules['_mysql_exceptions'] = pymysql.err" ./lib/fiveserver/storagecontroller.py

# 6. Run make install, forcing the same FSENV we already used above
# (the Makefile defaults to $(HOME)/fsenv, it must be overridden).
RUN make install FSENV=/opt/sixserver/sixserver-env

# Ensure execute permissions (the +x bit can get lost when cloning/copying the repo)
RUN chmod +x ./service.sh

RUN mkdir -p /var/log/sixserver


# ---------------------------------------------------------------------------
# Final stage: clean image, without gcc/build-essential/headers, much lighter
# ---------------------------------------------------------------------------
FROM python:2.7-slim

WORKDIR /opt/sixserver

# Copy only what was already compiled/installed from the builder stage
COPY --from=builder /opt/sixserver /opt/sixserver
COPY --from=builder /var/log/sixserver /var/log/sixserver

EXPOSE 10881/tcp 12881/tcp 13881/udp

CMD ["./service.sh", "run"]