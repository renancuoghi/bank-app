<template>
  <div class="q-pa-md">
    <div v-for="(item, index) in state.items" :key="index">
      <div class="row">
        <div class="col text-credit">
          <div class="text-caption">
            <strong>{{ item.user.username }}</strong>
          </div>
          <div class="text-caption">{{ item.created_at }}</div>
        </div>
        <div class="col self-end align-div" @click="goToTransaction(item.id)">
          <span
            v-bind:class="{
              'text-credit': item.transaction_type == 'C',
              'text-debit': item.transaction_type == 'D',
            }"
          >
            {{ formatCurrency(item.amount) }}
          </span>
        </div>
      </div>

      <div class="row div-sep">
        <div class="col">
          <q-separator color="blue-grey-2" />
        </div>
      </div>
    </div>
    <div class="flex flex-center" v-if="hasMoreData">
      <q-icon name="expand_more" :size="'md'" color="gray" @click="loadData" />
    </div>
  </div>
</template>

<script lang="ts">
import TransactionApi from 'src/api/transaction/transactionapi';
import { defineComponent, ref, computed } from 'vue';
import { IPaginationResponse } from 'src/models/shared/baseinterfaces';
import { ITransactionFull } from 'src/models/balance/balancetransaction';
import { useQuasar } from 'quasar';
import router from 'src/router';

import numberHelper from 'src/helpers/number';

export default defineComponent({
  name: 'TransactionPendingComponent',

  setup() {
    const $q = useQuasar();

    const api = new TransactionApi();

    const hasMoreData = computed(
      () => state.value.page <= state.value.total_pages
    );

    const state = ref({
      items: [] as ITransactionFull[],
      page: 1,
      pageSize: 10,
      total_pages: 1,
    });

    function loadedCallback(response: IPaginationResponse<ITransactionFull>) {
      state.value.page++;
      state.value.total_pages = response.data?.total_pages as number;
      state.value.items.push(...(response.data?.items as ITransactionFull[]));
    }

    function errorCallback(error: string) {
      $q.notify({
        message: error,
        color: 'danger',
      });
    }

    function formatCurrency(value: number): string {
      return numberHelper.toCurrency(value);
    }

    function listPendingAdmin() {
      api.listPendingTransactions(
        state.value.page,
        state.value.pageSize,
        loadedCallback,
        errorCallback
      );
    }

    function loadData() {
      if (state.value.page <= state.value.total_pages) {
        listPendingAdmin();
      }
    }

    function goToTransaction(id: number) {
      router.push({ name: 'checkdetail', params: { id: id } });
    }

    loadData();

    return {
      state,
      loadData,
      formatCurrency,
      hasMoreData,
      goToTransaction,
    };
  },
});
</script>
<style lang="scss" scoped>
.align-div {
  text-align: end;
}

.text-credit {
  color: #1976d2;
}

.text-debit {
  color: red;
}

.div-sep {
  padding-bottom: 10px;
  scroll-padding-bottom: 10px;
}
</style>
