<script setup>
import { reactive, ref } from 'vue';
import { useCustomerStore } from '../stores/customer'
import { RouterLink, useRouter } from 'vue-router'
import $ from 'jquery';
import 'jquery-validation';
const customerStore = useCustomerStore()
const router = useRouter()
const customer = reactive({
    first_name: '',
    last_name: '',
    email: '',
    phone: ''
});
const hasError = ref(false);
const handleSubmit = async () => {
    try {
        await customerStore.addCustomer(customer);
        router.push({ name: 'customer_list' })
    } catch (error) {
        console.log(error);
    }
};

$(document).ready(function () {
    $('#customerForm').validate({
        rules: {
            first_name: {
                required: true
            },
            last_name: {
                required: true
            },
            email: {
                required: true,
                email: true,
                remote: {
                    url: 'http://localhost/php_crud+vue/Backend/api/check_unique_api.php', // Your server-side endpoint to check email uniqueness
                    type: 'post',
                    data: {
                        email: function () {
                            return $('#email').val();
                        }
                    }
                },
            },
            phone: {
                required: true,
                minlength: 10,
                maxlength: 10,
                remote: {
                    url: 'http://localhost/php_crud+vue/Backend/api/check_unique_api.php', // Your server-side endpoint to check phone uniqueness
                    type: 'post',
                    data: {
                        phone: function () {
                            return $('#phone').val();
                        }
                    }
                },
            }
        },
        messages: {
            first_name: {
                required: "กรุณากรอกชื่อ"
            },
            last_name: {
                required: "กรุณากรอกนามสกุล"
            },
            email: {
                required: "กรุณากรอกอีเมล",
                email: "กรุณากรอกในรูปแบบอีเมล",
                remote: "อีเมลนี้ถูกใช้แล้ว"
            },
            phone: {
                required: "กรุณากรอกเบอร์โทรศัพท์",
                minlength: "โปรดกรอกความยาวไม่น้อยกว่า 10 ตัวอักษร",
                maxlength: "โปรดกรอกความยาวไม่น้อยกว่า 10 ตัวอักษร",
                remote: "เบอร์นี้ถูกใช้แล้ว"
            }
        },
        errorPlacement: function (error, element) {
            error.insertAfter(element.closest('.input-group')); // Place error message after the input-group div
            hasError.value = true; // Set hasError to true when there's an error
            element.closest('.input-group').removeClass('mb-3'); // Add class mb-3 and remove class mb-0
        },
        success: function (label, element) {
            hasError.value = false; // Set hasError to false when there's no error
            $(element).closest('.input-group').addClass('mb-0').removeClass('mb-3'); // Add class mb-0 and remove class mb-3
        },
        submitHandler: function (form) {
            handleSubmit();
        }
    });
});
</script>
<template>
    <div class="container">
        <h1 class="mt-4 text-center">Add Customer</h1>
        <form id="customerForm">
            <div class="input-group mb-3">
                <span class="input-group-text">ชื่อ</span>
                <input type="text" name="first_name" class="form-control" placeholder="ป้อนชื่อจริงของคุณ"
                    v-model="customer.first_name">
            </div>
            <div class="input-group mb-3">
                <span class="input-group-text">นามสกุล</span>
                <input type="text" name="last_name" class="form-control" placeholder="ป้อนนามสกุลของคุณ"
                    v-model="customer.last_name">
            </div>
            <div class="input-group mb-3">
                <span class="input-group-text">อีเมล</span>
                <input type="email" name="email" id="email" class="form-control" placeholder="ป้อนอีเมลของคุณ"
                    v-model="customer.email">
            </div>
            <div class="input-group mb-3">
                <span class="input-group-text">เบอร์</span>
                <input type="tel" name="phone" id="phone" class="form-control" placeholder="ป้อนเบอร์โทรศัพท์ของคุณ"
                    minlength="10" v-model="customer.phone" maxlength="10">
            </div>
            <div class="mb-3">
                <button type="submit" class="btn btn-success">เพิ่ม</button>
                <RouterLink :to="{ name: 'customer_list' }">
                    <button class=" btn btn-dark ms-2">กลับ</button>
                </RouterLink>
            </div>
        </form>
    </div>
</template>
<style>
.error {
    color: red;
}
</style>
