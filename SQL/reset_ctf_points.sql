-- ============================================
-- Script de réinitialisation des points CTF
-- ============================================
-- Ce script remet tous les points CTF à 0
-- Date: 2026-02-04
-- ============================================

UPDATE users SET ctf_points = 0;

-- Vérification
SELECT username, ctf_points FROM users;
