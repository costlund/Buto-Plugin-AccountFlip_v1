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

Mysql connection data. Parameter sql_org_name is optional to be able to retrieve name from an organization. This should be easier for user to select account in list view.

```
plugin:
  account:
    flip_v1:
      settings:
        mysql: 'yml:/../buto_data/theme/[theme]/mysql.yml'
        sql_org_name: |
          select name 
          from my_org
          inner join my_member on my_org.id=my_member.org_id
          where my_member.id=flip.account_id
          limit 1
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
