# Page snapshot

```yaml
- main:
  - link:
    - /url: http://localhost:8000
    - img
  - text: Have an account?
  - link "Sign In":
    - /url: http://localhost:8000/login
  - heading "Choisissez votre rôle" [level=1]
  - radio "Créateur Je veux offrir mes services et gérer mes événements." [checked]
  - img
  - text: Créateur Je veux offrir mes services et gérer mes événements.
  - radio "Client Je veux réserver des événements et services."
  - img
  - text: Client Je veux réserver des événements et services. 💸 Lorem ipsum is place text commonly? Lorem ipsum is placeholder text commonly used in the graphic, print, and publishing industries for previewing layouts.
  - checkbox "Switch label" [checked]
  - text: Switch label
  - link "<- Retour":
    - /url: http://localhost:8000
  - button "Continuer ->"
```