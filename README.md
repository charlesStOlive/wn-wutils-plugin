# wn-wutils-plugin
Ce plugin est l'utilitaire de base des plugins Waka. uniquement compatible avec Winter CMS.. Il apporte des méthodes qui facilitent la gestion des contrôleurs et des modèles. 


Il ajoute les behavior suivants : 
* WakaControllerBehavior 
* WakaReorderController 
* DuplicateModel (non fonctionnel pour l'instant

Il ajoute les traits suivants : 
* WakAnonymize 
* DBUtils 
* ConvertPx 
* ScopePeriodes


## Behaviors

### WakaControllerBehavior 
Ce behavior simplifie et harmonise la création d'un contrôleur. Si devTools est installé, vous pouvez créer directement un contrôleur avec la commande waka:contrôleurs
Ce que ce behavior modifie :
### create/render/preview.php
* Toutes les pages create, reorder, update appellent une fonction unique de rendu: 
```php
//create.php
<?= $this->wakaController->renderCreate() ?>

//update.php
<?= $this->wakaController->renderUpdate(true) ?>

//update.php
//deux arguments possibles $twoColumns = false, $showSecondaryTabs = false
// rendu de base
<?= $this->wakaController->renderUpdate() ?>
// twoColumns permet d'afficher sur deux colonnes | showSecondaryTabs permet d'afficher ou de cacher les tabs dans la seconde colonne
<?= $this->wakaController->renderUpdate(true, true) ?>
//preview.php
<?= $this->wakaController->renderPreview(true) ?>
```
### Configuration config_Waka.yaml 
Toutes classees de contrôleur exploitant WakaControllerBehavior doit avoir un fichier config_waka dans le répertoire du même nom que la classe. 

#### Exemples :
```yaml
modelClass: Vendor\Plugin\Models\Foo
backendUrl: vendor/plugin/foos
controllerConfig: 
    breadcrumb: 
        inSettings: false 
        title: Un titre
        rows: 
            index: 
                label: titre
                url: vendor/plugin/foos
    index: 
        base: 
            create: 
                show: true
                permissions: [vendor.plugin.*]
                label: Titre
                # url: wcli/crpf/aflscomites/create
                # icon: wn-icon-pen-nib
                # class: btn-secondary
            reorder: 
                false 
                permissions: []
            delete: 
                show: true
                permissions: [vendor.plugin.*]
            partials:
                index_btn:
                    url: $/vendor/plugin/controllers/foos/_index_btn.php
        update: 
            # partials:
            #     update_btn:
            #         url: $/vendor/plugin/controllers/foos/_update_btn.php

```
### Configuration : 
| clé | obligatoire | valeurs |
| --- | --- | --- |
| modelClass | OUI | La classee du modèle lié au contrôleur |
| backendUrl | OUI |  l'URL du backend |
| contrôleurConfig | OUI | La configuration du contrôleur ( voir configuration du contrôleur ci dessous )

### Configuration de  contrôleurConfig
#### Breadcrumb 
Configuration du breadcrumb 
| clé  | valeurs |
| --- | ---  |
| inSettings  | booléen, précise si le contrôleur est dans les settings |
| title  |  Le titre du breadcrumb |
| rows  | Un tableau de données, les nom des clés (ex :  index, pre_index) n'ont pas d'utilité mais chaque clé doit avoir un titre et une url
#### index 
Accepte deux valeurs principales base et partials. Dans la section partials il est possible de lier des partials, la partie base est une config des boutons, voir l'exemple
#### update 
Accepte une valeur partials. 


## Trait

### WakaControllerBehavior 