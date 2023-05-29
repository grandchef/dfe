```sh
openssl pkcs12 -in docs/certs/certificado.pfx -nodes >temp && <temp openssl pkcs12 -export -descert -out docs/certs/certificado2.pfx
```
