# 🛠️ Commandes de Développement

## Hot Reload CSS/JS (Recommandé)

```bash
# Démarrer le serveur de développement avec hot reload
npm run dev

# Si problème de port, forcer l'arrêt et redémarrer
pkill -f vite && npm run dev
```

## Build pour Production

```bash
# Construire les assets (quand fini de développer)
npm run build
```

## Laravel

```bash
# Démarrer le serveur Laravel
php artisan serve

# Vider les caches si problème
php artisan view:clear
php artisan config:clear
php artisan cache:clear
```

## Tests Playwright

```bash
# Tests d'authentification rapides
node playwright-tests/quick-auth-test.js

# Tests d'authentification complets
node playwright-tests/run-auth-tests.js

# Tests spécifiques
npx playwright test auth/login.spec.js
```

## Développement Optimal

**Workflow recommandé :**
1. `npm run dev` (dans un terminal)
2. `php artisan serve` (dans un autre terminal) 
3. Modifier le code → sauvegarde → rafraîchissement automatique ✨

**Configuration actuelle :**
- Vite: `http://127.0.0.1:5173/`
- Laravel: `http://localhost:8000`
- Apollo CSS + Tailwind CSS (ordre optimisé)

---
*Aide-mémoire généré le 20 Juin 2025*