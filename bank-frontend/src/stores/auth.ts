import { defineStore } from 'pinia';
import { ILoginResponse } from 'src/models/user/userinterface';

export const userStore = defineStore({
  id: 'userStore',
  state: () => ({
    user: {} as ILoginResponse | undefined,
    userIsLoggedIn: false as boolean,
    token: '' as string,
  }),

  actions: {
    setUserLoggedStorage(loggedUser: ILoginResponse) {
      this.user = loggedUser;

      this.userIsLoggedIn = true;
      this.token = this.user.data?.token as string;
      localStorage.setItem('user', JSON.stringify(loggedUser));
      localStorage.setItem('token', this.token);
    },
    cleanData() {
      localStorage.removeItem('user');
      localStorage.removeItem('token');
      this.userIsLoggedIn = false;
      this.user = undefined;
      this.token = '';
    },
    loadUser() {
      console.log(this.userIsLoggedIn);
      if (!this.userIsLoggedIn) {
        const userStorage = localStorage.getItem('user');
        if (userStorage) {
          this.user = JSON.parse(
            localStorage.getItem('user') as string
          ) as ILoginResponse;
          console.log(this.user);
          this.userIsLoggedIn = true;
          this.token = localStorage.getItem('token') as string;
        }
      }
    },
  },

  getters: {
    getUser(): ILoginResponse | null | undefined {
      return this.user;
    },
    getToken(): string {
      return this.token;
    },
    getUserIsLoggedIn(): boolean {
      return this.userIsLoggedIn;
    },
  },
});
