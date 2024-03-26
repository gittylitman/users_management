FROM bitnami/laravel

ENV AzureWebJobsScriptRoot=/home/site/wwwroot \
    AzureFunctionsJobHost__Logging__Console__IsEnabled=true \
    AzureWebJobsFeatureFlags=EnableWorkerIndexing \ 
    AzureWebJobsStorage=UseDevelopmentStorage=true 

WORKDIR /app
RUN cat php.ini >> /opt/bitnami/php/etc/php.ini
RUN composer install

COPY . /home/site/wwwroot
