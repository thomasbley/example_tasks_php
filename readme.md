PHP Example Tasks REST API
------------------------------------

[![Travis Build Status](https://travis-ci.com/thomasbley/example_tasks_php.svg?branch=master)](https://travis-ci.com/thomasbley/example_tasks_php)

#### Setup

    # setup composer
    docker-compose -f docker-compose.yml -f docker-compose-tools.yml run -u $(id -u) --rm composer

    # start containers
    docker-compose up
    docker-compose up -d

    # setup database
    docker-compose -f docker-compose.yml -f docker-compose-tools.yml run -u $(id -u) --rm shell update_database.php

    # generate bearer token for customer id 42
    docker-compose -f docker-compose.yml -f docker-compose-tools.yml run -u $(id -u) --rm shell generate_token.php 42

    # access/error logs
    docker-compose logs -f

    # start php container shell
    docker-compose exec php sh
    docker-compose exec -u $(id -u) php sh

    # start mysql client
    docker-compose exec mysql mysql -u root -proot tasks

    # show query log
    docker-compose exec mysql sh -c "tail -f /tmp/mysql.log"

    # start memcache client
    docker-compose -f docker-compose.yml -f docker-compose-tools.yml run -u $(id -u) --rm memcache_client

    # cleanup coverage
    rm -rf tests/coverage

    # remove containers/images/volumes
    docker-compose down
    docker images purge
    docker volume prune
    docker container prune
    docker system prune -a

#### Static code analyzers

    docker-compose -f docker-compose.yml -f docker-compose-tools.yml run -u $(id -u) --rm psalm
    docker-compose -f docker-compose.yml -f docker-compose-tools.yml run -u $(id -u) --rm phpcsfixer
    docker-compose -f docker-compose.yml -f docker-compose-tools.yml run -u $(id -u) --rm phploc

#### Tests, monitoring

    docker-compose -f docker-compose.yml -f docker-compose-tools.yml run -u $(id -u) --rm phpunit
    docker-compose -f docker-compose.yml -f docker-compose-tools.yml run -u $(id -u) --rm fpm_status
    docker-compose -f docker-compose.yml -f docker-compose-tools.yml run -u $(id -u) --rm memcache_status

#### Convert docs/api.md to docs/swaggerui/swagger.json

    docker-compose -f docker-compose.yml -f docker-compose-tools.yml run -u $(id -u) --rm apib2swagger

#### URLs

    http://127.0.0.1:8080/docs/
    http://127.0.0.1:8080/coverage/

#### Command line tests

    docker-compose -f docker-compose.yml -f docker-compose-tools.yml run -u $(id -u) --rm shell generate_token.php 42

    export TOKEN=...
    export BASE=http://127.0.0.1:8080

    curl -i -X POST -d '{"title":"test","duedate":"2020-05-22"}' -H "Authorization: Bearer ${TOKEN}" "${BASE}/v1/tasks"
    curl -i -X GET -H "Authorization: Bearer ${TOKEN}" "${BASE}/v1/tasks"
    curl -i -X PUT -d '{"title":"test","duedate":"2020-05-22","completed":"1"}' -H "Authorization: Bearer ${TOKEN}" "${BASE}/v1/tasks/1"
    curl -i -X GET -H "Authorization: Bearer ${TOKEN}" "${BASE}/v1/tasks?completed=1"
    curl -i -X GET -H "Authorization: Bearer ${TOKEN}" "${BASE}/v1/tasks/1"
    curl -i -X DELETE -H "Authorization: Bearer ${TOKEN}" "${BASE}/v1/tasks/1"