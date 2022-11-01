ARG APP_VERSION
ARG REPO

FROM ${REPO}:${APP_VERSION}

USER root

RUN apk update && \
    apk add ca-certificates curl bash openssl ansible gcc python3-dev libc-dev libffi-dev openssl-dev

COPY bin/install_certs.sh /tmp/install_certs.sh

RUN chmod a+x /tmp/install_certs.sh && /tmp/install_certs.sh
