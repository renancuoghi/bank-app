import { AxiosError, AxiosResponse } from 'axios';
import { IBalance } from 'src/models/balance/balancetransaction';
import { IResponse } from 'src/models/shared/baseinterfaces';
import {
  ILoginInput,
  ILoginResponse,
  IRegisterResponse,
  ISignUpInput,
} from 'src/models/user/userinterface';
import router from 'src/router';
import { userStore } from 'src/stores/auth';

import api from '../api';

class UserApi {
  async login(login: ILoginInput, callbackError?: (err: string) => void) {
    api
      .post<ILoginResponse>('api/auth/login', login)
      .then((response: AxiosResponse) => {
        const store = userStore();
        const userData = response.data as ILoginResponse;
        store.setUserLoggedStorage(userData);
        if (userData.data?.user.is_admin) {
          router.push({ name: 'admin-root' });
        } else {
          router.push({ name: 'home' });
        }
      })
      .catch((err: AxiosError) => {
        if (callbackError) {
          callbackError(err.response?.data.error);
        }
      });
  }

  async register(
    signUp: ISignUpInput,
    callbackOk: (response: IRegisterResponse) => void,
    callbackError?: (err: string) => void
  ) {
    api
      .post<IRegisterResponse>('api/auth/register', signUp)
      .then((response: AxiosResponse) => {
        callbackOk(response.data as IRegisterResponse);
        router.push({ name: 'login' });
      })
      .catch((err: AxiosError) => {
        if (callbackError) {
          callbackError(err.response?.data.error);
        }
      });
  }

  async getBalance(
    callbackOk: (response: IResponse<IBalance>) => void,
    callbackError?: (err: string) => void
  ) {
    api
      .get<IResponse<IBalance>>('api/user/balance')
      .then((response: AxiosResponse) => {
        callbackOk(response.data as IResponse<IBalance>);
      })
      .catch((err: AxiosError) => {
        if (callbackError) {
          callbackError(err.response?.data.error);
        }
      });
  }

  async logout(callbackError?: (err: string) => void) {
    api
      .get('api/user/logout')
      .then(() => {
        const store = userStore();
        store.cleanData();
        router.push({ name: 'login' });
      })
      .catch((err: AxiosError) => {
        if (callbackError) {
          callbackError(err.response?.data.error);
        }
      });
  }
}

export default UserApi;
