import React, {Component} from 'react'
import {Button, Container, Alert, Spinner, Row, Col} from 'react-bootstrap';
import {Formik, Field, ErrorMessage, useField, Form} from 'formik';
import * as Yup from 'yup';
import {Navigate} from 'react-router-dom';
import Header from './Header';
import Footer from './Footer';


const TextInput = ({ label, ...props }) => {
    const [field, meta] = useField(props);
    return (
        <Col sm={12}>
            <label htmlFor={props.id || props.name} className='form-label'>{label}</label>
            <input className="form-control" {...field} {...props} />
            {(meta.touched || meta.value) && meta.error ? (
                <div className='form-error'>
                    {meta.error}
                </div>
            ) : null}
        </Col>
    );
};

/**
 * The component responsible of rendering product form, validating and adding new product
 */
class ProductAdd extends Component {
    constructor(props) {
        super(props)
        this.state = {
            initialValues: {}, // Actual current form field values
            baseInitialValues: { // Base form field values that are unrelated to product specific fields
                sku: '',
                name: '',
                price: '',
                productType: '',
            },
            validationSchema: {}, // Actual current form validation schema
            baseValidationSchema: { // Base form validation schema
                sku: Yup.string()
                    .max(255, 'Must be 255 characters or less')
                    .required('Please enter product SKU'),
                name: Yup.string()
                    .max(255, 'Must be 255 characters or less')
                    .required('Please enter product name'),
                price: Yup.number()
                    .min(0)
                    .required('Please enter product price'),
                productType: Yup.string()
                    .oneOf(['DVD', 'Book', 'Furniture'])
                    .required('Please select product type'),
            },
            redirect: false
        }

        // Copy base values to current
        this.state.initialValues = { ...this.state.baseInitialValues };
        this.state.validationSchema = { ...this.state.baseValidationSchema };

        this.setRedirect = this.setRedirect.bind(this);
    }

    /**
     * Sets redirect state
     * @param boolean value
     */
    setRedirect(value) {
        this.setState({
            redirect: value
        });
    }

    /**
     * Takes current form values and sends addition request to backend
     * @param values
     * @returns response object
     */
    async setAddProduct(values) {
        const response = await fetch(import.meta.env.VITE_API_BACKEND + "/product", {
            method: 'POST',
            headers: {
              'Content-Type': 'application/json'
            },
            body: JSON.stringify(values)
        });

        return response;
    }

    /**
     * Triggers form changes whenever product type changes
     * It takes care of updating initialValues and validationSchema while preserving
     * the current entered form values
     * @param e event handler
     * @param field form field object
     * @param values current form values
     */
    onProductTypeChange = (e, field, values) => {
        const { baseValidationSchema, baseInitialValues } = this.state;
        const productType = e.target.value;
        let initialValues = new Map([
            ['productType', productType],
        ]);
        let validationSchema = {};

        field.onChange(e);

        switch (productType) {
            case 'DVD':
                initialValues.set('size', '');

                validationSchema = {
                    size: Yup.number()
                        .integer('DVD size must be an integer')
                        .positive('DVD size should be a positive number')
                        .required('DVD size is required'),
                };
                break;
            case 'Book':
                initialValues.set('weight', '');

                validationSchema = {
                    weight: Yup.number()
                        .positive('Book weight should be a positive number')
                        .required('Book weight is required'),
                };
                break;
            case 'Furniture':
                initialValues.set('height', '')
                    .set('width', '')
                    .set('length', '');

                validationSchema = {
                    height: Yup.number()
                        .positive('Furniture height should be a positive number')
                        .required('Furniture height is required'),
                    width: Yup.number()
                        .positive('Furniture width should be a positive number')
                        .required('Furniture width is required'),
                    length: Yup.number()
                        .positive('Furniture length should be a positive number')
                        .required('Furniture length is required'),
                };
                break;
        }

        /**
         * Only copy base values as we're not preserving product specific fields
         */
        let valuesMap = new Map(Object.entries(values));
        Object.entries(baseInitialValues).forEach(([key, value]) => {
            if (key !== 'productType') {
                initialValues.set(key, valuesMap.get(key));
            }
        });

        this.setState({
            initialValues: Object.fromEntries(initialValues),
            validationSchema: {
                ...baseValidationSchema,
                ...validationSchema,
            },
        });
    }

    /**
     * Takes a product type and returns product specific fields
     * @param type current selected product type
     * @returns rendered form fields
     */
    productForm(type) {
        let retVal = null;
        let infoBox = null;

        switch (type) {
            case 'DVD':
                infoBox = 'Please provide DVD size';
                retVal = (
                    <TextInput
                        name='size'
                        label='Size (MB)'
                        id='size'
                        type='number'
                    />
                );
                break;
            case 'Book':
                infoBox = 'Please provide Book weight';
                retVal = (
                    <TextInput
                        name='weight'
                        label='Weight (kg)'
                        id='weight'
                        type='number'
                    />
                );
                break;
            case 'Furniture':
                infoBox = 'Please specify Furniture dimensions in HxWxL';
                retVal = (
                    <>
                        <TextInput
                            name='height'
                            label='Height (cm)'
                            id='height'
                            type='number'
                        />
                        <TextInput
                            name='width'
                            label='Width (cm)'
                            id='width'
                            type='number'
                        />
                        <TextInput
                            name='length'
                            label='Length (cm)'
                            id='length'
                            type='number'
                        />
                    </>
                );
                break;
            default:
                return;

        }

        return (
            <Row sm={6} className="subform g-3" id={type}>
                {infoBox && (
                    <Col sm={12}>
                        <Alert variant='info'>
                            {infoBox}
                        </Alert>
                    </Col>
                )}
                {retVal}
            </Row>
        );
    }

    render() {
        const {
            validationSchema,
            initialValues,
            redirect,
            initialValues: { productType }
        } = this.state;

        return (
            <Formik
                enableReinitialize={true}
                initialValues={initialValues}
                validationSchema={Yup.object(validationSchema)}
                onSubmit={async (values, { setSubmitting, setFieldError }) => {
                    const response = await this.setAddProduct(values);
                    if (!response.ok) {
                        response.json().then(errors => {
                            for (const [field, error] of Object.entries(errors)) {
                                setFieldError(field, error);
                            }
                        });
                    } else {
                        this.setRedirect(true);
                    }
                    setSubmitting(false);
                }}
            >
                {({ isValid, isSubmitting, values }) => (
                    <Form id="product_form">
                        {redirect && (
                            <Navigate to="/" replace={true} />
                        )}
                        <Header>
                            <Col md={3} className='float-start'>
                                <h1>Add Product</h1>
                            </Col>
                            <div className='float-end'>
                                <Button variant='outline-primary' href='/' className='ms-1'>Cancel</Button>
                                <Button
                                    className='ms-1'
                                    variant='primary'
                                    type="submit"
                                    disabled={isSubmitting}
                                >
                                    {(isSubmitting && (
                                        <Spinner
                                            as="span"
                                            animation="border"
                                            size="sm"
                                            role="status"
                                            aria-hidden="true"
                                            className='me-1'
                                        />))
                                    }
                                    Save
                                </Button>
                            </div>
                        </Header>
                        <Container>
                            <Row className='g-5'>
                                <Col md={6}>
                                    <Row className='g-3'>
                                        <TextInput
                                            name='sku'
                                            label='SKU'
                                            id='sku'
                                            type='text'
                                        />
                                        <TextInput
                                            name='name'
                                            label='Name'
                                            id='name'
                                            type='text'
                                        />
                                        <TextInput
                                            name='price'
                                            label='Price'
                                            id='price'
                                            type='number'
                                        />
                                        <Col sm={12}>
                                            <label htmlFor='productType' className='form-label'>Product Type</label>
                                            <Field name='productType'>
                                                {({ field }) => (
                                                    <select className='form-control' id='productType' {...field} onChange={e => this.onProductTypeChange(e, field, values)}>
                                                        <option value=''>Please select a product type</option>
                                                        <option value='DVD'>DVD</option>
                                                        <option value='Book'>Book</option>
                                                        <option value='Furniture'>Furniture</option>
                                                    </select>
                                                )}
                                            </Field>
                                            <div className='form-error'>
                                                <ErrorMessage name='productType' />
                                            </div>
                                        </Col>
                                        {this.productForm(productType)}
                                    </Row>
                                </Col>
                            </Row>
                        </Container>
                        <Footer />
                    </Form>
                )}
            </Formik>
        );
    }
}


export default ProductAdd;