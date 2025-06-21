# Workflow de Création d'Événement - Multi-Step Form

## STEP 1 : Type de session
- **Radio buttons** : "Session individuelle" vs "Session de groupe"
- **Description courte** de chaque type
- **Si "groupe"** → Preview : "Vous devrez choisir un jeu spécifique"

## STEP 2 : Configuration de base
- **Nom de l'événement** (ex: "Coaching Valorant", "Soirée Among Us")
- **Description détaillée**
- **Durée** (dropdown 30min, 1h, 1h30, 2h, custom)
- **Prix par participant**

## STEP 3 : Jeu & Plateforme (conditionnel selon step 1)
- **Si individuel** : "Les clients choisiront dans votre liste de jeux" (info only)
- **Si groupe** : 
  - Dropdown plateformes (PC, PS5, Xbox, etc.)
  - Dropdown jeux selon la plateforme choisie
  - Niveau requis (optionnel)

## STEP 4 : Participants
- **Si individuel** : max_participants = 1 (hidden)
- **Si groupe** : 
  - Slider "Nombre max de participants" (2-10)
  - Checkbox "Session privée" (invitation only)

## STEP 5 : Plateforme de communication
- **Radio buttons** : Discord, Zoom, Google Meet, Teams
- **Input pour le lien personnalisé**
- **Preview du message** que recevront les clients

## STEP 6 : Récurrence (optionnel)
- **Checkbox** "Événement récurrent"
- **Si coché** : Dropdown "Toutes les semaines", "Toutes les 2 semaines", etc.
- **Sélection jour(s)** de la semaine

## STEP 7 : Validation & aperçu
- **Résumé complet** de l'événement
- **Preview** de ce que verront les clients
- **Bouton "Créer l'événement"**

---

## Données créées au final :
- **1 record** `event_types`
- **Records** `time_slots` générés selon les availabilities
- **Si récurrent** : planning automatique des prochaines occurrences

## Logique des champs event_types :
- **Individual** : `allow_game_choice = true`, `game_title` et `platform` = null
- **Group** : `allow_game_choice = false`, `game_title` et `platform` renseignés