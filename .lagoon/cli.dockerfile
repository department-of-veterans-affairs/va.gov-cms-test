FROM uselagoon/php-8.1-cli-drupal:latest

COPY .lagoon/ /app/.lagoon/
COPY .git/ /app/.git/
COPY patches/ /app/patches/
COPY hooks/ /app/hooks/
COPY simplesamlphp-config-metadata/ /app/simplesamlphp-config-metadata/
COPY composer.* /app/

COPY .tugboat/*.crt /usr/local/share/ca-certificates/
RUN update-ca-certificates

RUN composer install --no-dev
RUN bin/npm install

# Break out VA Theme compile into its component commands
RUN cd /app/bin && ln -sf ../docroot/libraries/yarn/bin/yarn yarn
RUN export NODE_EXTRA_CA_CERTS=/etc/pki/tls/certs/ca-bundle.crt PUPPETEER_SKIP_CHROMIUM_DOWNLOAD=TRUE; cd /app/docroot/core && yarn install
RUN cd /app/docroot/core && yarn build:css
RUN cd /app/docroot/design-system && yarn install && yarn build:drupal
RUN cd /app/docroot/themes/custom/vagovclaro && yarn install && yarn build

RUN composer va:web:install

COPY . /app
RUN mkdir --parents --verbose --mode=775 /app/docroot/sites/default/files

# Define where the Drupal Root is located
ENV WEBROOT=docroot

# Explicitly copy over env.ep
# If you’re using envplate, there is an entrypoint that will auto expand 
# the vars as long as the .env is in the WORKDIR
COPY .lagoon/.env.lagoon .env
