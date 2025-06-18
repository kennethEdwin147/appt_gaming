# Page snapshot

```yaml
- main:
  - link:
    - /url: http://localhost:8000
    - img
  - heading "Créer un compte client" [level=1]
  - text: Prénom *
  - textbox "Prénom *"
  - text: Nom *
  - textbox "Nom *"
  - text: Adresse e-mail *
  - textbox "Adresse e-mail *"
  - text: Mot de passe *
  - textbox "Mot de passe *"
  - text: Confirmer le mot de passe *
  - textbox "Confirmer le mot de passe *"
  - checkbox "Recevoir les actualités par email."
  - text: Recevoir les actualités par email.
  - button "C'est parti !"
  - text: Tu as dejà un compte ?
  - link "Connectes toi":
    - /url: http://localhost:8000/login
```