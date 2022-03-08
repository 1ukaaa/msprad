<?php

const NOM_DOSSIER_LOGS = 'LogsMSPRAuthentification';
const RECUL_DOSSIER_LOG = '../';

// variable en minutes
// DEFINITION : SI + 10 requêtes en - 60 secondes
const INTERVAL_REQUEST_TIME = '60';
const INTERVAL_REQUEST_NUMBER = '4';
// Temps de débloquage en secondes
const BLOCK_TIME = '10';

// Active Directory
const IP_AD = "192.168.161.138";
const ADMIN_READ_ONLY_OU_SOIGNANT = "SoignantAdministrateur@mspr.local";
CONST ADMIN_PASSWORD_READ_ONLY_OU_SOIGNANT = "MotDePasse1-";

// BDD
const HOST_BDD = 'localhost';
const LOGIN_BDD = 'nathan';
const MDP_BDD = 'root';