# Five'sOrder
<div align="center">
    <img alt="" src=".ksinf/logo.png" height="130px">
    <h3>Five'sOrdrer</h3>
    <em>Application web de gestion des commandes pour</em>
</div>

## Description

**Five'sOrder** est une application web intuitive conçue spécifiquement pour l'association Five'sTv, au nom de l'entreprise KS-Infinite, pour faciliter la gestion des commandes pendant l'événement For'Games. Cette plateforme permet une communication fluide entre les serveurs et les cuisiniers, optimisant ainsi le processus de préparation des repas et assurant une expérience culinaire sans faille pour les participants de l'événement.

## Présentation rapide

**Five'sOrder** est divisée en trois sections principales pour une gestion efficace :

### Partie Administrateur
- Ajouter et gérer les éléments du menu, leurs descriptions et leurs images.
- Ajouter et gérer les utilisateurs (cuisiniers et serveurs).
- Permettre la supervision et la maintenance de l'application pour une utilisation sans interruption.

### Partie Serveur
- Prendre les commandes des clients à partir d'une carte interactive.
- Indiquer les détails de la commande, y compris le menu choisi, le nom du serveur et le numéro unique de la commande.
- Envoyer les commandes directement aux cuisiniers pour préparation.

### Partie Cuisinier
- Recevoir et afficher les commandes envoyées par les serveurs.
- Voir les détails complets de chaque commande.
- Indiquer lorsque les commandes sont terminées pour une communication en temps réel avec les serveurs.

## Design

**Five'sOrder** est conçue avec une interface utilisateur simple et intuitive, garantissant une utilisation rapide et efficace. L'application est accessible et compréhensible pour tous les utilisateurs, quel que soit leur niveau de compétence technologique. Le design épuré et les fonctionnalités clairement définies assurent une adoption rapide et une utilisation quotidienne fluide.

## Screenshot
[A venir...]

## URL

L'application sera accessible via un sous-domaine du site Five'sTv :
[https://fivesorder.fivestv.fr](https://fivesorder.fivestv.fr)

## Installation

### Installation par défaut

1. Clonez le dépôt :
    ```bash
    git clone https://github.com/KSInfinite/fivesorder.git
    ```
2. Accédez au répertoire du projet :
    ```bash
    cd fivesorder
    ```
3. Installez les packages NPM :
    ```bash
    npm install
    ```
4. Installez les packages Composer :
    ```bash
    composer install
    ```
5. Configurez les informations nécessaires dans `config.yml`.
6. Lancez l'application en local via MAMP, WAMP ou autre.

### Installation version RTU (Ready To Use)

1. Installez la dernière version via :
    [https://github.com/KSInfinite/fivesorder/releases/latest](https://github.com/KSInfinite/fivesorder/releases/latest)
2. Configurez les informations nécessaires dans `config.yml`.
3. Uploadez le site sur internet ou en local.
4. Connectez-vous en tant qu'administrateur et modifiez le mot de passe de connexion en tant qu'administrateur si besoin.

## Utilisation

1. Ouvrez votre navigateur et accédez à [https://fivesorder.fivestv.fr](https://fivesorder.fivestv.fr).
2. Connectez-vous avec vos identifiants administrateur, serveur, ou cuisinier.
3. Suivez les instructions à l'écran pour ajouter des éléments au menu, prendre des commandes ou gérer les préparations.

## Contribution

Les contributions sont les bienvenues ! Veuillez suivre les étapes suivantes pour contribuer :

1. Forkez le dépôt.
2. Créez votre branche de fonctionnalité (`git checkout -b feature/AmazingFeature`).
3. Commitez vos modifications (`git commit -m 'Add some AmazingFeature'`).
4. Poussez votre branche (`git push origin feature/AmazingFeature`).
5. Ouvrez une Pull Request.

## Licence

Distribué sous la licence MPL-2.0. Voir `LICENSE` pour plus d'informations.

## Contact

Pour toute question, vous pouvez ouvrir une issue

<p align="center">
  <img align="center" src=".ksinf/fivestv.png" width="100" />
  <img align="center" src=".ksinf/ksinf.png" width="100" /> 
</p>
