<template>
  <div class="q-pa-md text-white">
    <div class="row">
      <div class="col">
        <div class="text-caption">Current balance:</div>
        <div class="text-subtitle1">{{ balanceFormat }}</div>
      </div>
    </div>
  </div>
</template>

<script lang="ts">
import { useQuasar } from 'quasar';
import UserApi from 'src/api/user/userapi';
import { IBalance } from 'src/models/balance/balancetransaction';
import { IResponse } from 'src/models/shared/baseinterfaces';
import { computed, defineComponent, ref } from 'vue';

import numberHelper from 'src/helpers/number';

export default defineComponent({
  name: 'BalanceTotalComponent',

  setup() {
    const $q = useQuasar();

    const balanceValue = ref(0);

    const balanceFormat = computed(() =>
      numberHelper.toCurrency(balanceValue.value)
    );

    const api = new UserApi();
    function loadBalance() {
      api.getBalance(
        (response: IResponse<IBalance>) => {
          balanceValue.value = response.data?.total as number;
        },
        (err: string) => {
          $q.notify({
            message: err,
            color: 'danger',
          });
        }
      );
    }
    loadBalance();
    return {
      balanceValue,
      balanceFormat,
    };
  },
});
</script>
<style lang="scss" scoped>
.align-div {
  text-align: end;
}
</style>
