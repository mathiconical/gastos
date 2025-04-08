<h1 align="center">Gastos</h1>

<p align="center">
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/l/laravel/framework" alt="License"></a>
</p>

## Sobre

A primeiro momento, para armazenar informaçoes de preços de produtos e valores de impostos em compras que geram NFC-e, rastreaveis pela Sefaz.

- Construida com Laravel e Filament com PHP 8.4.4.
- Docker image otimizada.
- Uso de sqlite.

### Dependencias do Projeto

- **[Docker](https://docker.com)**
- **[Laravel](https://laravel.com)**
- **[Filament](https://filamentphp.com/)**
- **[Make (Opcional)](https://www.gnu.org/software/make/manual/make.html)**


## Executar o Projeto
1.
```
docker build -t php --tag php8.4.4-fpm-alpine3.21 .
```

2.
```
docker run -it --name php -v ./app/:/usr/app \
    --network appnet \
    -w /usr/app
    -e APP_ENV=local \
    -p 9000:9000 \
    --privileged \
    --user=root \
    --entrypoint /bin/sh \
    localhost/php8.4.4-fpm-alpine3.21 \
    -c "composer install && php artisan serve --host=0.0.0.0 --port=9000"
```

3.
```
abrir o browser **http://localhost:9000/admin**
email: test@mail.com
senha: password
```

## Uso

- Consiga um Cupom Fiscal de alguma compra de supermercado, mercado ou qualquer uma NFCe que seja rastreavel pela **[Sefaz](https://portalsped.fazenda.mg.gov.br/portalnfce/sistema/consultaarg.xhtml)**.
- Leia o QR Code ou copie a chave da NFCe composto por 44 caracteres, por exemplo: _31236549785820000139650010000706931123456789_
- Apos o login, va ate entrada, adicione um cupom e salve.
- Apos salvar, o cupom sera processado e ira criar registros em compras e unidades.
