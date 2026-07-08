FROM python:2.7-slim AS builder

# 1. Parche de repositorios históricos
RUN echo "deb http://archive.debian.org/debian/ buster main" > /etc/apt/sources.list && \
    echo "deb http://archive.debian.org/debian-security/ buster/updates main" >> /etc/apt/sources.list && \
    echo "Acquire::Check-Valid-Until \"false\";" > /etc/apt/apt.conf.d/10no--check-valid-until

# Necesitamos make, un compilador C (gcc/build-essential) y los headers de Python
# (python2.7-dev) para que las extensiones nativas de Twisted compilen sin problema.
# Estas herramientas solo existen en esta etapa "builder" y NO viajan a la imagen final.
RUN apt-get update && apt-get install -y make build-essential python2.7-dev && rm -rf /var/lib/apt/lists/*

RUN pip install "virtualenv<16.0.0"

# service.sh espera TODO bajo /opt/sixserver (sixserver-env, lib, etc/sixserver.tac),
# así que usamos esa misma ruta como raíz del proyecto en vez de /app.
WORKDIR /opt/sixserver
COPY . /opt/sixserver

# 2. HACK: Removemos MySQL-python, pyOpenSSL, Twisted y zope.interface del archivo
# original porque los instalamos manualmente en pasos posteriores con versiones
# compatibles; dejar la línea original (ej. zope.interface==3.8.0, de 2011) haría
# que "make install" intente reinstalar una versión vieja encima y probablemente falle.
RUN sed -i '/MySQL-python/d' ./pip.requirements && \
    sed -i '/pyOpenSSL/d' ./pip.requirements && \
    sed -i '/Twisted/d' ./pip.requirements && \
    sed -i '/zope.interface/d' ./pip.requirements

# 3. Forzamos la creación del entorno virtual en la ruta que service.sh espera (FSENV)
RUN virtualenv /opt/sixserver/sixserver-env

# 4. INSTALACIÓN MANUAL
# --without-extensions NO existe como opción real en el setup.py de Twisted 10.2.0
# ni en el de zope.interface (por eso fallaba con "option not recognized").
# Como ya tenemos gcc y los headers de Python instalados, dejamos que compile
# normalmente sus extensiones C.
RUN /opt/sixserver/sixserver-env/bin/pip install "pymysql<1.0" "pyOpenSSL==17.5.0"
RUN /opt/sixserver/sixserver-env/bin/pip install "zope.interface"
RUN /opt/sixserver/sixserver-env/bin/pip install "Twisted==10.2.0" --no-deps

# 5. Parcheamos dinámicamente para usar PyMySQL emulando a MySQLdb
# Confirmado: el único archivo que usa MySQLdb es ./lib/fiveserver/storagecontroller.py
# También hace "import _mysql_exceptions" por separado (viene con MySQL-python);
# lo alias a pymysql.err, que define las mismas excepciones DB-API 2.0.
RUN sed -i "1i import sys\nimport pymysql\nimport pymysql.err\npymysql.install_as_MySQLdb()\nsys.modules['_mysql_exceptions'] = pymysql.err" ./lib/fiveserver/storagecontroller.py

# 6. Ejecutamos make install, forzando el mismo FSENV que ya usamos arriba
# (el Makefile por defecto usa $(HOME)/fsenv, hay que sobreescribirlo).
RUN make install FSENV=/opt/sixserver/sixserver-env

# Aseguramos permisos de ejecución (el bit +x puede perderse al clonar/copiar el repo)
RUN chmod +x ./service.sh

RUN mkdir -p /var/log/sixserver


# ---------------------------------------------------------------------------
# Etapa final: imagen limpia, sin gcc/build-essential/headers, mucho más liviana
# ---------------------------------------------------------------------------
FROM python:2.7-slim

WORKDIR /opt/sixserver

# Copiamos solo lo ya compilado/instalado desde la etapa builder
COPY --from=builder /opt/sixserver /opt/sixserver
COPY --from=builder /var/log/sixserver /var/log/sixserver

EXPOSE 10881/tcp 12881/tcp 13881/udp

CMD ["./service.sh", "run"]