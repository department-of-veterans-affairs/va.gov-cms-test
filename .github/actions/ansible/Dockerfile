FROM alpine 

ENV ANSIBLE_HOST_KEY_CHECKING=False

COPY install_certs.sh /tmp/install_certs.sh
RUN chmod a+x /tmp/install_certs.sh && /tmp/install_certs.sh

RUN apk update && \
    apk add ansible gcc python3-dev libc-dev libffi-dev openssl-dev
    
COPY entrypoint.sh /entrypoint.sh



ENTRYPOINT ["/entrypoint.sh"]
