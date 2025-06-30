import * as msal from '@azure/msal-browser'

const requestedScopes = {
    scopes: ["User.Read", "Mail.Read"] // Scopes avec majuscules cohérentes
}

const msalInstance = new msal.PublicClientApplication({
    auth: {
        clientId: process.env.VUE_APP_OAUTH_CLIENT_ID,
    },
    cache: {
        cacheLocation: "sessionStorage"
    }
})

let isInitialized = false;

async function ensureInitialized() {
    if (!isInitialized) {
        await msalInstance.initialize();
        isInitialized = true;
    }
}

export async function signInAndGetUser() {
    try {
        await ensureInitialized();

        const authResult = await msalInstance.loginPopup({
            ...requestedScopes,
            prompt: "consent",
        });

        msalInstance.setActiveAccount(authResult.account);

        const mails = await getMails();

        return {
            user: authResult.account,
            mails
        };
    } catch (error) {
        console.error("Erreur lors de la connexion:", error);
        throw error;
    }
}

export async function getMails() {
    try {
        await ensureInitialized();

        const account = msalInstance.getActiveAccount();
        if (!account) {
            throw new Error("Aucun compte actif");
        }

        let tokenResponse;
        try {
            // Tentative d'acquisition silencieuse du token
            tokenResponse = await msalInstance.acquireTokenSilent({
                scopes: ["Mail.Read"],
                account
            });
        } catch (silentError) {
            console.warn("Acquisition silencieuse échouée, tentative avec popup:", silentError);
            // Si l'acquisition silencieuse échoue, utiliser un popup
            tokenResponse = await msalInstance.acquireTokenPopup({
                scopes: ["Mail.Read"],
                account
            });
        }

        const accessToken = tokenResponse.accessToken;

        if (!accessToken) {
            throw new Error("Token d'accès non disponible");
        }

        // Appel à l'API Microsoft Graph
        const graphResponse = await fetch("https://graph.microsoft.com/v1.0/me/messages?$top=10", {
            headers: {
                'Authorization': `Bearer ${accessToken}`,
                'Content-Type': 'application/json'
            }
        });

        if (!graphResponse.ok) {
            let errorDetails;
            const contentType = graphResponse.headers.get('content-type');

            try {
                if (contentType && contentType.includes('application/json')) {
                    errorDetails = await graphResponse.json();
                } else {
                    errorDetails = await graphResponse.text();
                }
                console.error("Détails de l'erreur Graph API:", errorDetails);
            } catch (parseError) {
                console.error("Impossible de parser la réponse d'erreur:", parseError);
                errorDetails = `Erreur ${graphResponse.status}`;
            }

            throw new Error(`Erreur API Graph : ${graphResponse.status} ${graphResponse.statusText} - ${typeof errorDetails === 'object' ? JSON.stringify(errorDetails) : errorDetails}`);
        }

        const data = await graphResponse.json();
        console.log("Emails récupérés:", data.value);
        return data.value;

    } catch (error) {
        console.error("Erreur dans getMails:", error);
        throw error;
    }
}

// Fonction utilitaire pour se déconnecter
export async function signOut() {
    try {
        await ensureInitialized();
        const account = msalInstance.getActiveAccount();
        if (account) {
            await msalInstance.logoutPopup({
                account
            });
        }
    } catch (error) {
        console.error("Erreur lors de la déconnexion:", error);
        throw error;
    }
}

// Fonction pour récupérer un mail spécifique par son ID
export async function getMailById(mailId) {
    try {
        await ensureInitialized();

        const account = msalInstance.getActiveAccount();
        if (!account) {
            throw new Error("Aucun compte actif");
        }

        let tokenResponse;
        try {
            tokenResponse = await msalInstance.acquireTokenSilent({
                scopes: ["Mail.Read"],
                account
            });
        } catch (silentError) {
            console.warn("Acquisition silencieuse échouée, tentative avec popup:", silentError);
            tokenResponse = await msalInstance.acquireTokenPopup({
                scopes: ["Mail.Read"],
                account
            });
        }

        const accessToken = tokenResponse.accessToken;

        if (!accessToken) {
            throw new Error("Token d'accès non disponible");
        }

        // Appel à l'API Microsoft Graph pour un mail spécifique
        const graphResponse = await fetch(`https://graph.microsoft.com/v1.0/me/messages/${mailId}`, {
            headers: {
                'Authorization': `Bearer ${accessToken}`,
                'Content-Type': 'application/json'
            }
        });

        if (!graphResponse.ok) {
            let errorDetails;
            const contentType = graphResponse.headers.get('content-type');

            try {
                if (contentType && contentType.includes('application/json')) {
                    errorDetails = await graphResponse.json();
                } else {
                    errorDetails = await graphResponse.text();
                }
                console.error("Détails de l'erreur Graph API:", errorDetails);
            } catch (parseError) {
                console.error("Impossible de parser la réponse d'erreur:", parseError);
                errorDetails = `Erreur ${graphResponse.status}`;
            }

            throw new Error(`Erreur API Graph : ${graphResponse.status} ${graphResponse.statusText} - ${typeof errorDetails === 'object' ? JSON.stringify(errorDetails) : errorDetails}`);
        }

        const mailData = await graphResponse.json();
        console.log("Mail détaillé récupéré:", mailData);
        return mailData;

    } catch (error) {
        console.error("Erreur dans getMailById:", error);
        throw error;
    }
}

// Fonction utilitaire pour vérifier si l'utilisateur est connecté
export function isSignedIn() {
    return msalInstance.getActiveAccount() !== null;
}