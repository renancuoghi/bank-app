<template>
  <div class="q-pa-md">
    <q-form class="q-gutter-md" @submit="onSubmit">
      <q-input
        outlined
        v-model="state.amount"
        type="number"
        label="AMOUNT"
        prefix="$"
        :rules="[(val) => (val && val.length > 0) || 'Amount is required']"
      />

      <q-input
        filled
        v-model="dateString"
        :rules="[(val) => (val && val.length > 0) || 'Date is required']"
      >
        <template v-slot:prepend>
          <q-icon name="event" class="cursor-pointer">
            <q-popup-proxy
              cover
              transition-show="scale"
              transition-hide="scale"
            >
              <q-date v-model="state.date" mask="YYYY-MM-DD">
                <div class="row items-center justify-end">
                  <q-btn v-close-popup label="Close" color="primary" flat />
                </div>
              </q-date>
            </q-popup-proxy>
          </q-icon>
        </template>
      </q-input>

      <q-input
        v-model="state.description"
        label="DESCRIPTION"
        :rules="[(val) => (val && val.length > 0) || 'Description is required']"
      />

      <div class="fixed-bottom" style="margin: 15px">
        <q-btn
          label="ADD PURCHASE"
          class="register-button"
          type="submit"
          color="primary"
          :size="'lg'"
        />
      </div>
    </q-form>
  </div>
</template>

<script lang="ts">
import { useQuasar } from 'quasar';
import TransactionApi from 'src/api/transaction/transactionapi';
import {
  ITransaction,
  TransactionType,
} from 'src/models/balance/balancetransaction';
import { IResponseSuccess } from 'src/models/shared/baseinterfaces';
import { computed, defineComponent, ref } from 'vue';

export default defineComponent({
  name: 'PurchaseForm',

  setup() {
    const $q = useQuasar();
    const state = ref({
      amount: 0,
      date: new Date().toISOString(),
      description: '',
      dateVisibility: false,
    });

    const dateString = computed(() => state.value.date?.toLocaleString());

    function onSubmit() {
      const transactionApi = new TransactionApi();
      transactionApi.create(
        {
          amount: state.value.amount,
          description: state.value.description,
          transaction_type: TransactionType.DEBIT,
          image: '',
          created_at: state.value.date,
        },
        (response: IResponseSuccess<ITransaction>) => {
          state.value = {
            amount: 0,
            date: new Date(),
            description: '',
            dateVisibility: false,
          };
          $q.notify({
            message: response.message,
            color: 'positive',
          });
        },
        (error: string) => {
          $q.notify({
            message: error,
            color: 'negative',
          });
        }
      );
    }

    return {
      state,
      dateString,
      onSubmit,
    };
  },
});
</script>
<style lang="scss" scoped>
.register-button {
  width: 100%;
}
</style>
