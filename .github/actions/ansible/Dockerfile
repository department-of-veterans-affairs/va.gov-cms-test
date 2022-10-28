FROM alpine 

ENV ANSIBLE_HOST_KEY_CHECKING=False

RUN apk update && \
    apk add ansible gcc python3-dev libc-dev libffi-dev openssl-dev ca-certificates curl bash openssl
    
COPY install_certs.sh /tmp/install_certs.sh    
COPY entrypoint.sh /entrypoint.sh

RUN chmod a+x /tmp/install_certs.sh && /tmp/install_certs.sh

ENTRYPOINT ["/entrypoint.sh"]
