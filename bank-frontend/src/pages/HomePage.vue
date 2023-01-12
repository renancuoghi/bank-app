<template>
  <q-page>
    <CurrentBalanceComponent />
    <TransactionListComponent transaction-type="ALL" />
  </q-page>
</template>

<script lang="ts">
import { defineComponent } from 'vue';
import CurrentBalanceComponent from 'src/components/Header/CurrentBalanceComponent.vue';
import TransactionListComponent from 'src/components/Balance/Transaction/TransactionListComponent.vue';

export default defineComponent({
  name: 'HomePage',

  components: {
    CurrentBalanceComponent,
    TransactionListComponent,
  },
});
</script>
