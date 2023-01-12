import { defineStore } from 'pinia';
import { IOption } from 'src/models/shared/baseinterfaces';

export const useConfigStore = defineStore('configStore', {
  state: () => ({
    dateSelector: {
      option: '',
      label: '',
    },
  }),
  getters: {
    hasCurrentDate: (state) => state.dateSelector.option !== '',
    getCurrentDate: (state) => state.dateSelector,
    getCurrentMonthQuery: (state): string => {
      if (state.dateSelector.option !== '') {
        return state.dateSelector.option;
      }
      return new Date().toISOString().split('T')[0];
    },
  },
  actions: {
    setCurrentDate(dateSelector: IOption) {
      this.dateSelector = dateSelector;
    },
  },
});
