import { IEntity, IResponseSuccess } from '../shared/baseinterfaces';

export interface ILoginInput {
  username: string;
  password: string;
}

export interface ISignUpInput extends ILoginInput {
  email: string;
}

export interface IUser extends ISignUpInput, IEntity {
  is_admin: boolean;
}

export interface ILoginOutput {
  token: string;
  user: IUser;
}

export type ILoginResponse = IResponseSuccess<ILoginOutput>;
export type IRegisterResponse = IResponseSuccess<IUser>;
