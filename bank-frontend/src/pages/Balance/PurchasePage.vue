<template>
  <q-page>
    <div class="q-pa-md bg-cyan text-white">
      <div class="row">
        <div class="col">
          <BalanceTotalComponent />
        </div>
      </div>
    </div>
    <PurchaseForm />
  </q-page>
</template>

<script lang="ts">
import { defineComponent } from 'vue';
import PurchaseForm from 'src/components/Balance/Form/PurchaseForm.vue';
import BalanceTotalComponent from 'src/components/Balance/Transaction/BalanceTotalComponent.vue';
export default defineComponent({
  name: 'PurchasePage',

  components: {
    PurchaseForm,
    BalanceTotalComponent,
  },
});
</script>
