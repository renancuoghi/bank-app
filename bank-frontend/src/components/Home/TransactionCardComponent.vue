<template>
  <div class="q-pa-md bg-cyan-1 text-blue">
    <div class="row">
      <div class="col-8">
        <div class="text-caption">{{ label }}</div>
        <div class="text-subtitle1">{{ formatCurrency(transactionValue) }}</div>
      </div>
      <div class="col-4 self-end align-div">
        <div class="vertical-middle" style="text-align: center">
          <div class="text-subtitle1">
            <q-btn flat round color="primary" icon="add" @click="goToRoute" />
          </div>
          <div class="text-caption">{{ description }}</div>
        </div>
      </div>
    </div>
  </div>
</template>

<script lang="ts">
import router from 'src/router';
import { defineComponent, ref } from 'vue';
import numberHelper from 'src/helpers/number';

export default defineComponent({
  name: 'TransactionCardComponent',

  props: {
    label: String,
    description: String,
    routePath: String,
    transactionValue: Number,
  },
  setup(props) {
    const balanceValue = ref(0);

    function formatCurrency(value: number | undefined): string {
      return numberHelper.toCurrency(value as number);
    }

    function goToRoute() {
      router.push({ name: props.routePath });
    }
    return {
      balanceValue,
      goToRoute,
      formatCurrency,
    };
  },
});
</script>
<style lang="scss" scoped>
.align-div {
  text-align: end;
}
</style>
