import { defineStore } from "pinia";

export const useUserStore = defineStore("user", {
  state: () => ({
    user: null,
  }),

  actions: {
    setUser(newUser) {
      this.user = newUser;
    },

    clearUser() {
      this.user = null;
    },

    updateMail(updatedMail) {
      if (!this.user || !this.user.mails) return;

      const index = this.user.mails.findIndex(
        (mail) => mail.id === updatedMail.id
      );
      if (index !== -1) {
        this.user.mails.splice(index, 1, { ...updatedMail });
      }
    },

    toggleRead(mailId) {
      if (!this.user || !this.user.mails) return;

      const mail = this.user.mails.find((m) => m.id === mailId);
      if (mail) {
        mail.isRead = !mail.isRead;
        this.updateMail(mail);
      }
    },

    toggleFavorite(mailId) {
      if (!this.user || !this.user.mails) return;

      const mail = this.user.mails.find((m) => m.id === mailId);
      if (mail) {
        mail.isFavorite = !mail.isFavorite;
        this.updateMail(mail);
      }
    },

    toggleHidden(mailId) {
      if (!this.user || !this.user.mails) return;

      const mail = this.user.mails.find((m) => m.id === mailId);
      if (mail) {
        mail.isHidden = !mail.isHidden;
        this.updateMail(mail);
      }
    },
  },

  persist: true,
});
