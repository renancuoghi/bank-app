<template>
  <q-page>
    <div class="q-pa-md bg-cyan text-white">
      <DateSelectorComponent />
    </div>
    <TransactionListComponent transaction-type="D" />

    <div class="fixed-bottom-right" style="margin: 15px">
      <q-btn
        round
        type="button"
        color="primary"
        icon="add"
        :size="'lg'"
        @click="goToPurchase"
      />
    </div>
  </q-page>
</template>

<script lang="ts">
import { defineComponent } from 'vue';
import DateSelectorComponent from 'src/components/Tools/DateSelectorComponent.vue';
import TransactionListComponent from 'src/components/Balance/Transaction/TransactionListComponent.vue';
import router from 'src/router';

export default defineComponent({
  name: 'ExpensesPage',

  components: {
    TransactionListComponent,
    DateSelectorComponent,
  },

  setup() {
    function goToPurchase() {
      router.push({ name: 'purchase' });
    }

    return {
      goToPurchase,
    };
  },
});
</script>
