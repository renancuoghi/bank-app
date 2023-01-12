<template>
  <div class="q-pa-md">
    <q-form class="q-gutter-md">
      <div>
        <div class="fieldTitle">
          <q-icon name="person" /> <label>Customer:</label>
        </div>
        <span class="fieldValue">{{ transaction?.user.username }}</span>
      </div>

      <div>
        <div class="fieldTitle">
          <q-icon name="mail" /> <label>Email:</label>
        </div>
        <span class="fieldValue">{{ transaction?.user.email }}</span>
      </div>

      <div>
        <div class="fieldTitle">
          <q-icon name="description" /> <label>Account:</label>
        </div>
        <span class="fieldValue">{{ transaction?.balance_id }}</span>
      </div>

      <div>
        <div class="fieldTitle">
          <q-icon name="payments" /> <label>Reported amount:</label>
        </div>
        <span class="fieldValue">{{
          formatCurrency(transaction?.amount)
        }}</span>
      </div>

      <div>
        <q-img
          :src="transaction?.path_image"
          spinner-color="white"
          loading="lazy"
        />
      </div>

      <div class="fixed-bottom-left" style="margin: 15px">
        <q-btn
          label="REJECT"
          class="register-button"
          type="button"
          :size="'lg'"
          icon="cancel"
          @click="reject"
          :loading="loading"
        />
      </div>
      <div class="fixed-bottom-right" style="margin: 15px">
        <q-btn
          label="ACCEPT"
          icon="check_circle"
          class="register-button"
          type="button"
          :size="'lg'"
          color="primary"
          @click="accept"
          :loading="loading"
        />
      </div>
    </q-form>
  </div>
</template>

<script lang="ts">
import { useQuasar } from 'quasar';
import TransactionApi from 'src/api/transaction/transactionapi';
import { ITransactionFull } from 'src/models/balance/balancetransaction';
import { IResponseSuccess } from 'src/models/shared/baseinterfaces';
import { defineComponent, onMounted, ref } from 'vue';
import { useRoute } from 'vue-router';
import numberHelper from 'src/helpers/number';
import router from 'src/router';

export default defineComponent({
  name: 'CheckDetailForm',

  setup() {
    const route = useRoute();
    const api = new TransactionApi();
    const $q = useQuasar();

    const transaction = ref<ITransactionFull>();
    const loading = ref(true);

    function formatCurrency(value: number | undefined): string {
      if (value === undefined) {
        return '';
      }
      return numberHelper.toCurrency(value);
    }

    function loadTransaction(id: number) {
      const $q = useQuasar();
      api.getById(
        id,
        (register: IResponseSuccess<ITransactionFull>) => {
          transaction.value = register.data as ITransactionFull;
          loading.value = false;
        },

        (error: string) => {
          loading.value = false;
          $q.notify({
            message: error,
            color: 'negative',
          });
        }
      );
    }

    function accept() {
      const id = transaction.value?.id as number;
      api.accept(
        id,
        () => {
          router.push({ name: 'admin-root' });
        },

        (error: string) => {
          $q.notify({
            message: error,
            color: 'negative',
          });
        }
      );
    }

    function reject() {
      const id = transaction.value?.id as number;
      api.reject(
        id,
        () => {
          router.push({ name: 'admin-root' });
        },

        (error: string) => {
          $q.notify({
            message: error,
            color: 'negative',
          });
        }
      );
    }

    onMounted(() => {
      const id: number = +route.params.id;
      loadTransaction(id);
    });

    return {
      transaction,
      formatCurrency,
      accept,
      reject,
      loading,
    };
  },
});
</script>
<style lang="scss" scoped>
.register-button {
  width: 100%;
}

.fieldValue {
  color: $primary;
  font-weight: bold;
  font-size: 18px;
}

.fieldTitle {
  color: $blue-grey-12;
  font-size: 14px;
}
</style>
