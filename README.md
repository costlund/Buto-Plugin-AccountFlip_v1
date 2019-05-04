# Buto-Plugin-AccountFlip_v1

Flip between accounts. 
When sign in to one account one could add another account by typing username and password. 
Each account will be grouped equal and user are able to jump from one to another.


## Settings
```
plugin_modules:
  account_flip:
    plugin: 'account/flip_v1'
```
```
plugin:
  account:
    flip_v1:
      settings:
        mysql: 'yml:/../buto_data/theme/[theme]/mysql.yml'
```


## Schema
```
/plugin/account/flip_v1/mysql/schema.yml
```

## Javascript

Open Bootstrap modal from theme Javascript.

```
PluginWfBootstrapjs.modal({id: 'modal_account_flip', url: '/account_flip/view', lable: this.innerHTML, size: 'xl'})
```
