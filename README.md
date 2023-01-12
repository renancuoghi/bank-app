
# Bank App

Project developed using laravel and vuejs. The goal of this project is develop a simple 
check deposit control.



## Project information


| Path   | Description                           |
| :---------- | :---------------------------------- |
| `bank-backend` | Backend developed in laravel |
| `bank-frontend` | Frontend developed in vuejs3 + quasar + typescript |

## Installing

### Docker 
```bash
docker-compose up -d
# installing composer, key and run migrates and seeders
docker exec backend sh laravel_install.sh
```
On web browser: 
http://localhost:9000

Please, this project was made for mobile perpective, so open as device mobile mode.

Common user: 
username: usertest
pass: password

Admin user:
username: admin
pass: password


#### Test

I developed all the tests using phpunit, you can find them in test path.

To execute: 
```bash
  # into docker
  docker exec -it backend bash
  php artisan test
  # out
  docker exec backend php artisan test
```


## Licença

[MIT](https://choosealicense.com/licenses/mit/)
