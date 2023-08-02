IOT module monitoring website & simulator
=========================================

## Requirements
- docker-compose
- make

## Installation
1. Download project
2. In project root run `make install`

## Testing
In project root run `make test`

## Website
After successful start of the docker's containers open http://localhost/ to see predefined modules and measurement types. To creare/modify ones goto http://localhost/admin

## Simulator
Go to console project root and run `make shell`, then in container's shell run `php bin/console app:simulate`. 

By default simulator runs in real-time mode for 10 minutes. See `php bin/console app:simulate --help` for other options. 

Try to refresh website main page while simulator runs!

## Code style quality
In project root 

`make psalm`

`make csfix`