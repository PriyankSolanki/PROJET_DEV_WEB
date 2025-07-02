import * as msal from '@azure/msal-browser'

const requestedScopes = {
    scopes : ["User.read", "Mail.read"]
}

const msalInstance = new msal.PublicClientApplication({
    auth : {
        clientId : process.env.VUE_APP_OAUTH_CLIENT_ID,
    },
    cache : {
        cacheLocation : "sessionStorage"
    }
})


let isInitialized = false;

async function ensureInitialized() {
    if (!isInitialized) {
        await msalInstance.initialize();
        isInitialized = true;
    }
}
export async function signInAndGetUser () {
    await ensureInitialized();
    const authResult = await msalInstance.loginPopup({
        ...requestedScopes,
        prompt: "consent",
    })
    msalInstance.setActiveAccount(authResult.account)
    const mails = await getMails();
    return {
        user: authResult.account,
        mails
    };
}

export async function logout() {
    await msalInstance.logoutPopup({
        postLogoutRedirectUri: "/",
    });
}


export async function getMails() {
    const account = msalInstance.getActiveAccount();
    if (!account) throw new Error("Aucun compte actif");

    let tokenResponse;
    try {
        tokenResponse = await msalInstance.acquireTokenSilent({
            scopes: ["Mail.Read"],
            account
        });
    } catch (e) {
        tokenResponse = await msalInstance.acquireTokenPopup({
            scopes: ["Mail.Read"]
        });
    }

    const accessToken = tokenResponse.accessToken;

    const graphResponse = await fetch("https://graph.microsoft.com/v1.0/me/messages?$top=10", {
        headers: {
            Authorization: `Bearer ${accessToken}`
        }
    });

    if (!graphResponse.ok) {
        throw new Error("Erreur API Graph : " + graphResponse.statusText);
    }

    const data = await graphResponse.json();
    console.log(data.value);
    return data.value;
}