<script setup>
import { reactive, onMounted } from "vue";
import { useCustomerStore } from '../stores/customer'
import { RouterLink, useRoute } from 'vue-router'
const customerStore = useCustomerStore()
const route = useRoute()
const customer = reactive({
  first_name: '',
  last_name: '',
  email: '',
  phone: ''
});
const customerID = route.params.id;
onMounted(async () => {
  await customerStore.showDetailCustomer(customerID);
  customer.first_name = customerStore.customer.first_name
  customer.last_name = customerStore.customer.last_name
  customer.email = customerStore.customer.email
  customer.phone = customerStore.customer.phone
});


</script>
<template>
  <div class="container">
    <h1 class="mt-4 text-center">Customer Detail</h1>
    <form>
      <div class="input-group mb-3">
        <span class="input-group-text">ชื่อ</span>
        <input type="text" class="form-control" placeholder="ป้อนชื่อจริงของคุณ" v-model="customer.first_name" readonly>
      </div>
      <div class="input-group mb-3">
        <span class="input-group-text">นามสกุล</span>
        <input type="text" class="form-control" placeholder="ป้อนนามสกุลของคุณ" v-model="customer.last_name" readonly>
      </div>
      <div class="input-group mb-3">
        <span class="input-group-text">อีเมล</span>
        <input type="email" class="form-control" placeholder="ป้อนอีเมลของคุณ" v-model="customer.email" readonly>
      </div>
      <div class="input-group mb-3">
        <span class="input-group-text">เบอร์</span>
        <input type="tel" class="form-control" placeholder="ป้อนเบอร์โทรศัพท์ของคุณ" minlength="12"
          v-model="customer.phone" maxlength="12" readonly>
      </div>
      <div class="mb-3">
        <RouterLink :to="{ name: 'customer_list' }">
          <button class=" btn btn-dark ms-2">กลับ</button>
        </RouterLink>
      </div>
    </form>
  </div>
</template>
