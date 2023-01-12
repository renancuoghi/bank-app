<template>
  <div class="q-pa-md">
    <div v-for="(item, index) in state.items" :key="index">
      <div class="row">
        <div class="col text-credit">
          <div class="text-caption">
            <strong>{{ item.description }}</strong>
          </div>
          <div class="text-caption">{{ item.created_at }}</div>
        </div>
        <div class="col self-end align-div">
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
import { computed, defineComponent, ref, watch } from 'vue';
import { IPaginationResponse } from 'src/models/shared/baseinterfaces';
import {
  ITransaction,
  ITransactionFull,
  TransactionStatus,
  TransactionType,
} from 'src/models/balance/balancetransaction';
import { useQuasar } from 'quasar';
import { useConfigStore } from 'src/stores/config';
import { userStore } from 'src/stores/auth';
import numberHelper from 'src/helpers/number';

export default defineComponent({
  name: 'TransactionListComponent',
  props: {
    period: {
      type: String,
      required: false,
    },
    transactionStatus: {
      type: String,
      default: TransactionStatus.ACCEPTED,
    },
    transactionType: {
      type: String,
      default: TransactionType.CREDIT,
    },
    dateOption: {
      type: String,
      required: false,
    },
  },
  setup(props) {
    const $q = useQuasar();
    const config = useConfigStore();

    const api = new TransactionApi();

    const hasMoreData = computed(
      () => state.value.page <= state.value.total_pages
    );

    watch(
      () => [props.dateOption],
      () => {
        state.value.page = 1;
        state.value.pageSize = 10;
        state.value.items = [];
        state.value.total_pages = 1;
        loadData();
      }
    );

    const state = ref({
      items: [] as ITransaction[] | ITransactionFull,
      page: 1,
      pageSize: 10,
      total_pages: 1,
    });

    function loadedCallback(response: IPaginationResponse<ITransaction>) {
      state.value.page++;
      state.value.total_pages = response.data?.total_pages as number;
      state.value.items.push(...(response.data?.items as ITransaction[]));
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

    // listPendingTransactions
    function loadCredit() {
      api.listCredit(
        config.getCurrentMonthQuery,
        props.transactionStatus,
        state.value.page,
        state.value.pageSize,
        loadedCallback,
        errorCallback
      );
    }

    function listDebit() {
      api.listDebit(
        config.getCurrentMonthQuery,
        state.value.page,
        state.value.pageSize,
        loadedCallback,
        errorCallback
      );
    }

    function listLasts() {
      api.listLastsTransactions(
        config.getCurrentMonthQuery,
        state.value.page,
        state.value.pageSize,
        loadedCallback,
        errorCallback
      );
    }

    function loadData() {
      if (state.value.page <= state.value.total_pages) {
        // user routes
        if (props.transactionType == TransactionType.CREDIT) {
          loadCredit();
        } else if (props.transactionType == TransactionType.DEBIT) {
          listDebit();
        } else {
          listLasts();
        }
      }
    }

    loadData();

    return {
      state,
      loadData,
      hasMoreData,
      formatCurrency,
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
