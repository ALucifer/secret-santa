# TODO


- [ ] check les templates de mail
  - [x] Nouveau membre
- [ ] template mobile
  - [x] home
  - [x] login
  - [x] register
  - [x] mot de passe oublié
  - [x] profile a completer
  - [x] secret santa
  - [x] profile
  - [ ] ma liste de cadeau
  - [ ] mon santa
- [x] voir pourquoi la création d'un secret santa ne fonctionne plus
- [x] Corriger les erreurs JS/TS
- [x] Voir si à la connexion d'un nouveau member il propose le pseudo et le mot de passe à changer
  - [x] voir pourquoi on ne peut plus ajouter de membre
- [ ] Exposer toutes les routes d'api pour passer par le router "maison"
- [ ] passer en composant global
  - [ ] input
  - [ ] button
- [ ] Ne passer que par des DTO pour les objets exposé au front
  - [ ] Chaque DTO devra avoir une factory qui prendra en parametre l'entité en question
- [ ] tests
    - [ ] Création d'un nouveau membre + user lors de l'ajout sur un secret santa
    - [ ] Création d'un nouveau membre quand le user existe deja
    - [ ] Affichage et mise à jour d'un user qui se connecte pour la premiere fois
- [ ] toutes les urls en /api doivent renvoyer du json