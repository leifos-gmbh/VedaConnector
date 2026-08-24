Generating/updating the api:
Added "--skip-validate-spec", the json file is not usable otherwise
``` shell
java -jar openapi-generator-cli.jar generate -i 2026-08-24_veda_rest.json -g php -o out --additional-properties invokerPackage="Leifos\VedaConnector\GeneratedOpenApi" --skip-validate-spec

rsync -avz --delete out/lib/ ../lib
rm -rf out
```
