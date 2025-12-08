---
name: Security Vulnerability
about: Signaler une faille de sécurité (⚠️ Utiliser Security Advisory pour les vulnérabilités critiques)
title: '[SECURITY] '
labels: 'security, high-priority'
assignees: ''
---

<!-- 🚨 CRITICAL: For critical vulnerabilities, use GitHub Security Advisory instead -->
<!-- 🤖 AUTOMATION: Auto-escalates to security team, triggers incident response -->
<!-- 🏷️ AUTO-LABELS: critical, high, medium, low, cve, cwe, owasp -->
<!-- 📊 AUTO-PROJECT: Added to security incident board -->

## ⚠️ AVERTISSEMENT & Disclosure Responsable

**Pour les vulnérabilités critiques, utilisez plutôt GitHub Security Advisory :**
`Security` > `Advisories` > `New draft security advisory`

**Politique de disclosure responsable :**
- [ ] 🔒 Privé - Ne pas divulguer publiquement
- [ ] 🕐 Délai de correction souhaité : 90 jours
- [ ] 📧 Contact security team : security@company.com
- [ ] 🤝 Coordination avec l'équipe de sécurité

Cette issue publique ne devrait être utilisée que pour des problèmes de sécurité mineurs ou des améliorations de sécurité.

**Procédure d'urgence :**
- [ ] 🚨 Incident critique - Activer plan d'urgence immédiatement
- [ ] 📞 Contacter le Security Lead : +XX-XXX-XXX-XXXX
- [ ] 🔔 Notifier toutes les équipes concernées
- [ ] 📝 Documenter dans l'incident response system

---

## 🔒 Type de problème de sécurité

- [ ] 🚨 Vulnérabilité critique (utiliser Security Advisory!)
- [ ] ⚠️ Injection SQL / XSS / CSRF
- [ ] 🔐 Problème d'authentification / autorisation
- [ ] 🔑 Gestion des secrets / tokens
- [ ] 📝 Validation des données insuffisante
- [ ] 🌐 Configuration CORS problématique
- [ ] 📦 Dépendance vulnérable
- [ ] 🛡️ Amélioration de sécurité générale

## 📋 Description du problème

<!-- Description détaillée du problème de sécurité -->

## 🎯 Impact Potentiel & Évaluation Risque

**Sévérité CVSS :**
- Score CVSS : X.X
- Vecteur : 
- Niveau : [ ] 🔴 Critique (9.0-10.0) [ ] 🟠 Haute (7.0-8.9) [ ] 🟡 Moyenne (4.0-6.9) [ ] 🟢 Basse (0.1-3.9)

**Impact métier :**
- [ ] 💰 Perte financière potentielle
- [ ] 👥 Impact vie privée utilisateurs
- [ ] ⚖️ Non-conformité RGPD/GDPR
- [ ] 🏢 Réputation entreprise
- [ ] 📊 Vol de données propriétaires
- [ ] 🔐 Accès non autorisé aux systèmes

**Données / Systèmes affectés :**
- [ ] Données personnelles (PII)
- [ ] Données financières
- [ ] Données santé (si applicable)
- [ ] Secrets/API keys
- [ ] Base de données complète
- [ ] Système de fichiers
- [ ] Infrastructure réseau
- [ ] Logs et monitoring

**Portée de l'impact :**
- [ ] 🌐 Tous les environnements (prod, staging, dev)
- [ ] 🏭 Production uniquement
- [ ] 👥 Sous-ensemble d'utilisateurs
- [ ] 📊 Données spécifiques
- [ ] 🔧 Configuration système

**Exploitabilité :**
- [ ] 🔄 Exploitable à distance
- [ ] 👤 Nécessite authentification
- [ ] 🌐 Accès réseau requis
- [ ] 💻 Accès physique requis
- [ ] 🤖 Automatisable

## 🔍 Étapes pour reproduire

1. 
2. 
3. 

## 💻 Preuve de concept (PoC)

```bash
# Exemple de requête malveillante ou code démonstratif
# NE PAS inclure de PoC fonctionnel pour les vulnérabilités critiques
```

## ✅ Solution recommandée

<!-- Comment corriger cette vulnérabilité ? -->

## 🔧 Environnement affecté

- [ ] Production
- [ ] Staging
- [ ] Développement

**Versions affectées :**
<!-- v1.0.0 - v1.2.3 -->

## 📚 Références

<!-- CVE, CWE, OWASP, articles de sécurité pertinents -->
- OWASP Top 10 : 
- CWE : 
- Autres références :

## 📌 Tâches de Correction & Incident Response

**Phase 1 - Triage immédiat (< 1h) :**
- [ ] 🚨 Confirmer la vulnérabilité
- [ ] 📞 Activer l'équipe de sécurité
- [ ] 📝 Créer ticket incident
- [ ] 🔒 Isoler les systèmes si nécessaire
- [ ] 📊 Évaluer l'impact initial

**Phase 2 - Analyse (< 24h) :**
- [ ] 🔍 Analyse technique complète
- [ ] 🎯 Identifier tous les systèmes affectés
- [ ] 📈 Évaluer l'impact réel
- [ ] 🔎 Rechercher d'autres instances
- [ ] 📋 Documenter la preuve de concept

**Phase 3 - Développement correctif (< 72h) :**
- [ ] 🛠️ Développer le patch
- [ ] 🧪 Tests unitaires et sécurité
- [ ] 🔬 Tests d'intégration
- [ ] 📝 Documentation technique
- [ ] 🔄 Review de sécurité

**Phase 4 - Déploiement :**
- [ ] 🚀 Déployer en environnement de test
- [ ] ✅ Validation complète
- [ ] 🌍 Déployer en production
- [ ] 📊 Monitoring post-déploiement
- [ ] 📧 Communication aux utilisateurs

**Phase 5 - Post-incident :**
- [ ] 📊 Rapport d'incident
- [ ] 🎯 Actions préventives
- [ ] 📚 Mise à jour documentation
- [ ] 🧪 Amélioration tests sécurité
- [ ] 🔄 Review processus

**Communication externe :**
- [ ] 📧 Notification utilisateurs
- [ ] 📢 Communication publique
- [ ] 📋 Security advisory
- [ ] 🏢 Notification autorités (si requis)
- [ ] 📊 Rapport transparence

## 🔐 Mesures Temporaires & Monitoring

**Mitigations immédiates :**
- [ ] 🔥 Désactiver la fonctionnalité vulnérable
- [ ] 🛡️ Ajouter des règles firewall/WAF
- [ ] 🔒 Restreindre les accès IP
- [ ] 📊 Augmenter le monitoring
- [ ] 🚨 Activer les alertes en temps réel
- [ ] 🔄 Forcer la déconnexion des utilisateurs

**Monitoring renforcé :**
- [ ] 📈 Logs d'accès suspects
- [ ] 🔍 Recherche de patterns d'attaque
- [ ] 📊 Métriques d'utilisation anormales
- [ ] 🚨 Alertes sur tentatives d'exploitation
- [ ] 📝 Audit trail complet

**Mesures organisationnelles :**
- [ ] 👥 Sensibilisation équipe
- [ ] 📚 Formation sécurité
- [ ] 🔄 Review processus
- [ ] 📋 Mise à jour politiques

**Coordination externe :**
- [ ] 🏢 Contact CERT national
- [ ] 🤝 Coordination fournisseurs
- [ ] 📢 Information communauté (si applicable)
- [ ] ⚖️ Contact autorités réglementaires

## 🤖 Automatisation & Intégration Sécurité

**GitHub Security Integration :**
- [ ] 📋 Créer Security Advisory
- [ ] 🏷️ Appliquer labels automatiques
- [ ] 📊 Ajouter au projet sécurité
- [ ] 🔔 Notifier security team

**CI/CD Security Pipeline :**
- [ ] 🧪 Tests sécurité automatisés
- [ ] 🔍 SAST (Static Analysis)
- [ ] 🌐 DAST (Dynamic Analysis)
- [ ] 📦 Dependency scanning
- [ ] 🚫 Bloquer les déploiements

**Monitoring & Alerting :**
- [ ] 📊 Dashboard sécurité
- [ ] 🚨 Alertes temps réel
- [ ] 📈 Métriques d'incidents
- [ ] 📝 Rapports automatisés

**Documentation & Runbooks :**
- [ ] 📚 Playbook incident response
- [ ] 🔧 Procédures de correction
- [ ] 📋 Checklist sécurité
- [ ] 🎓 Formation équipe

**Compliance & Audit :**
- [ ] ⚖️ Traçabilité réglementaire
- [ ] 📊 Logs d'audit
- [ ] 🔍 Preuves de conformité
- [ ] 📋 Rapports d'audit

## 💬 Informations additionnelles

**Références externes :**
**Contacts sécurité :**
**Historique incidents similaires :**
**Autre :**