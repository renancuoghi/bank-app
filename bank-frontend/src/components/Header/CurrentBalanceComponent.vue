<template>
  <div class="q-pa-md bg-primary text-white">
    <div class="row">
      <div class="col">
        <BalanceTotalComponent />
      </div>
      <div class="col self-end align-div">
        <DateSelectorComponent @dateSelectedChange="dateSelectedChange" />
      </div>
    </div>
  </div>
  <div class="q-pa-md bg-cyan-1 text-white">
    <TransactionCardComponent
      label="Incomes"
      description="DEPOSIT A CHECK"
      route-path="deposit"
      :transaction-value="state.incoming"
    />

    <TransactionCardComponent
      label="Expenses"
      description="PURCHASE"
      route-path="purchase"
      :transaction-value="state.expenses"
    />
  </div>
</template>

<script lang="ts">
import { defineComponent, ref } from 'vue';
import DateSelectorComponent from '../Tools/DateSelectorComponent.vue';
import TransactionCardComponent from '../Home/TransactionCardComponent.vue';
import BalanceTotalComponent from '../Balance/Transaction/BalanceTotalComponent.vue';
import { useQuasar } from 'quasar';
import TransactionApi from 'src/api/transaction/transactionapi';
import { IOption, IResponse } from 'src/models/shared/baseinterfaces';
import { ITotalTransactionType } from 'src/models/balance/balancetransaction';
import { useConfigStore } from 'src/stores/config';

export default defineComponent({
  name: 'CurrentBalanceComponent',

  components: {
    DateSelectorComponent,
    TransactionCardComponent,
    BalanceTotalComponent,
  },

  setup() {
    const $q = useQuasar();
    const config = useConfigStore();
    const api = new TransactionApi();

    const state = ref({
      incoming: 0,
      expenses: 0,
    });

    function loadTotal() {
      api.getTotalTransactionType(
        config.getCurrentMonthQuery,
        (response: IResponse<ITotalTransactionType>) => {
          state.value.incoming = response.data?.incoming as number;
          state.value.expenses = response.data?.expenses as number;
        },
        (err: string) => {
          $q.notify({
            message: err,
            color: 'negative',
          });
        }
      );
    }

    function dateSelectedChange(newOption: IOption) {
      loadTotal();
    }

    loadTotal();
    return {
      state,
      dateSelectedChange,
    };
  },
});
</script>
<style lang="scss" scoped>
.align-div {
  text-align: end;
}
</style>
