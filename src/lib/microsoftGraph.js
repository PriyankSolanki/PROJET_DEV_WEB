import * as msal from '@azure/msal-browser'

const requestedScopes = {
    scopes : ["User.read"]
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
        await msalInstance.initialize(); // ← Important !
        isInitialized = true;
    }
}
export async function signInAndGetUser () {
    await ensureInitialized();
    const authResult = await msalInstance.loginPopup(requestedScopes)
    msalInstance.setActiveAccount(authResult.account)
    return authResult.account
}