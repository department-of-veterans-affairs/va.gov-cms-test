ARG APP_VERSION
ARG REPO

FROM ${REPO}:${APP_VERSION}

USER root

RUN apk update && \
    apk add ca-certificates curl bash openssl ansible gcc python3-dev libc-dev libffi-dev openssl-dev

COPY install_certs.sh /tmp/install_certs.sh
COPY requirements.txt /tmp/requirements.txt

RUN chmod a+x /tmp/install_certs.sh && /tmp/install_certs.sh
RUN pip3 install -r requirements.txt
