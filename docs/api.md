FORMAT: 1A
HOST: http://127.0.0.1:8080
VERSION: 1

# Example Tasks PHP API

Example API for managing tasks.

# Group Tasks

## GET /v1/tasks{?completed}
Get current or completed tasks

+ Parameters
    + completed: `1` (enum[string], optional)

+ Request (application/json)
    + Headers

            Authorization: Bearer {token}

+ Response 200 (application/json)
    + Attributes (array[Task], fixed-type)

+ Response 401

+ Response 500 (application/json)
    + Attributes (InternalServerError)

## GET /v1/tasks{taskId}

Get single task

+ Parameters
    + taskId (number)

+ Request (application/json)
    + Headers

            Authorization: Bearer {token}

+ Response 200 (application/json)
    + Attributes (Task)

+ Response 401

+ Response 404

+ Response 500 (application/json)
    + Attributes (InternalServerError)

# Data Structures

## Task (object)
+ id (number, required)
+ title (string, required)
+ duedate (string, required) - date, YYYY-mm-dd
+ completed (boolean, required)

## InternalServerError (object)
+ error: `internal server error` (enum[string], required)
