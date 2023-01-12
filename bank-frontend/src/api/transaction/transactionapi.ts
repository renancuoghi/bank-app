import { AxiosError, AxiosResponse } from 'axios';
import {
  ITotalTransactionType,
  ITransaction,
  ITransactionFull,
  ITransactionInput,
} from 'src/models/balance/balancetransaction';
import {
  IPaginationResponse,
  IResponse,
  IResponseSuccess,
} from 'src/models/shared/baseinterfaces';
import api from '../api';

class TransactionApi {
  async create(
    transaction: ITransactionInput,
    callbackOk: (response: IResponseSuccess<ITransaction>) => void,
    callbackError?: (err: string) => void
  ) {
    api
      .post<IResponseSuccess<ITransaction>>(
        'api/transaction/create',
        transaction
      )
      .then((response: AxiosResponse) => {
        callbackOk(response.data as IResponseSuccess<ITransaction>);
      })
      .catch((err: AxiosError) => {
        if (callbackError) {
          callbackError(err.response?.data.error);
        }
      });
  }

  async listCredit(
    date: string,
    status: string,
    page: number,
    pagesize: number,
    callbackOk: (response: IPaginationResponse<ITransaction>) => void,
    callbackError?: (err: string) => void
  ) {
    const endpoint = `api/transaction/credit?date=${date}&page=${page}&page_size=${pagesize}&status=${status}`;
    api
      .get(endpoint)
      .then((response: AxiosResponse) => {
        callbackOk(response.data as IPaginationResponse<ITransaction>);
      })
      .catch((err: AxiosError) => {
        if (callbackError) {
          callbackError(err.response?.data.error);
        }
      });
  }

  async listDebit(
    date: string,
    page: number,
    pagesize: number,
    callbackOk: (response: IPaginationResponse<ITransaction>) => void,
    callbackError?: (err: string) => void
  ) {
    const endpoint = `api/transaction/debit?date=${date}&page=${page}&page_size=${pagesize}`;
    api
      .get(endpoint)
      .then((response: AxiosResponse) => {
        callbackOk(response.data as IPaginationResponse<ITransaction>);
      })
      .catch((err: AxiosError) => {
        if (callbackError) {
          callbackError(err.response?.data.error);
        }
      });
  }

  async listLastsTransactions(
    date: string,
    page: number,
    pagesize: number,
    callbackOk: (response: IPaginationResponse<ITransaction>) => void,
    callbackError?: (err: string) => void
  ) {
    const endpoint = `api/transaction/lasts?date=${date}&page=${page}&page_size=${pagesize}`;
    api
      .get(endpoint)
      .then((response: AxiosResponse) => {
        callbackOk(response.data as IPaginationResponse<ITransaction>);
      })
      .catch((err: AxiosError) => {
        if (callbackError) {
          callbackError(err.response?.data.error);
        }
      });
  }

  async getTotalTransactionType(
    date: string,
    callbackOk: (response: IResponse<ITotalTransactionType>) => void,
    callbackError?: (err: string) => void
  ) {
    const endpoint = `api/transaction/total-transaction-type?date=${date}`;
    api
      .get(endpoint)
      .then((response: AxiosResponse) => {
        callbackOk(response.data as IResponse<ITotalTransactionType>);
      })
      .catch((err: AxiosError) => {
        if (callbackError) {
          callbackError(err.response?.data.error);
        }
      });
  }

  async listPendingTransactions(
    page: number,
    pagesize: number,
    callbackOk: (response: IPaginationResponse<ITransactionFull>) => void,
    callbackError?: (err: string) => void
  ) {
    const endpoint = `api/transaction/pending?page=${page}&page_size=${pagesize}`;
    api
      .get(endpoint)
      .then((response: AxiosResponse) => {
        callbackOk(response.data as IPaginationResponse<ITransactionFull>);
      })
      .catch((err: AxiosError) => {
        if (callbackError) {
          callbackError(err.response?.data.error);
        }
      });
  }

  async getById(
    id: number,
    callbackOk: (response: IResponseSuccess<ITransactionFull>) => void,
    callbackError?: (err: string) => void
  ) {
    const endpoint = `api/transaction/getbyid/${id}`;
    api
      .get(endpoint)
      .then((response: AxiosResponse) => {
        callbackOk(response.data as IResponseSuccess<ITransactionFull>);
      })
      .catch((err: AxiosError) => {
        if (callbackError) {
          callbackError(err.response?.data.error);
        }
      });
  }

  async accept(
    id: number,
    callbackOk: (response: IResponseSuccess<number>) => void,
    callbackError?: (err: string) => void
  ) {
    const endpoint = `api/transaction/accept/${id}`;
    api
      .get(endpoint)
      .then((response: AxiosResponse) => {
        callbackOk(response.data as IResponseSuccess<number>);
      })
      .catch((err: AxiosError) => {
        if (callbackError) {
          callbackError(err.response?.data.error);
        }
      });
  }

  async reject(
    id: number,
    callbackOk: (response: IResponseSuccess<number>) => void,
    callbackError?: (err: string) => void
  ) {
    const endpoint = `api/transaction/reject/${id}`;
    api
      .get(endpoint)
      .then((response: AxiosResponse) => {
        callbackOk(response.data as IResponseSuccess<number>);
      })
      .catch((err: AxiosError) => {
        if (callbackError) {
          callbackError(err.response?.data.error);
        }
      });
  }
}

export default TransactionApi;
