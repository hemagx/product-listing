import React, {Component} from 'react'
import {Container, Button, Spinner, Col, Row} from 'react-bootstrap'
import Header from './Header'
import Footer from './Footer'
import ProductView from './ProductView'

/**
 * The compnent responsible of rendering products list page, fetching and mass deleting those from server
 */
class ProductsList extends Component {
    constructor(props) {
        super(props)
        this.state = {
            deleteDisable: false,
            products: new Map(),
            checkedProducts: new Map(),
            loading: true,
            isDeleting: false
        };

        this.deleteProduct = this.deleteProduct.bind(this);
    }

    /**
     * Marks a product for deletion and adds it to checkedProducts state
     * @param {*} id product id
     */
    onCheckDelete(id) {
        const { checkedProducts } = this.state;
        const Products = new Map(checkedProducts);

        if (!Products.has(id)) {
            Products.set(id, true);
        } else {
            Products.delete(id);
        }

        this.setState({
            checkedProducts: Products
        });
    }

    /**
     * Fetches products from backend and populates products state
     */
    getFetchProduct() {
        fetch(import.meta.env.VITE_API_BACKEND + "/product/list")
            .then(res => res.json())
            .then(result => this.setState({
                products: new Map(Object.entries(result)),
                loading: false
            })).catch(console.log)
    }

    /**
     * sends a request to delete products using checkedProducts state
     * @returns JSON object of deleted products ids
     */
    async setDeleteProduct() {
        const { checkedProducts } = this.state;
        const response = await fetch(import.meta.env.VITE_API_BACKEND + "/product", {
            method: 'DELETE',
            headers: {
              'Content-Type': 'application/json'
            },
            body: JSON.stringify(Object.fromEntries(checkedProducts))
        });

        return response.json();
    }

    /**
     * Initiate the products deletion process and sets deleteDisable, isDeleting and products state
     * once a response is recieved
     */
    deleteProduct() {
        const { deleteDisable } = this.state;

        if (deleteDisable) {
            return;
        }

        this.setState({
            deleteDisable: true,
            isDeleting: true
        });

        this.setDeleteProduct().then(deletedProducts => {
            const { products: productList } = this.state;
            const products = new Map(productList);

            for (const productId of deletedProducts) {
                products.delete(productId);
            }

            this.setState({
                deleteDisable: false,
                isDeleting: false,
                products: products,
                checkedProducts: new Map()
            });
        })
    }

    /**
     * Fetches products post component rendering
     */
    componentDidMount() {
        this.getFetchProduct();
    }

    render() {
        const {
            products,
            isDeleting,
            loading,
            deleteDisable
        } = this.state;
        let productViews = [];

        /**
         * Creating an array of <ProductView> from products array
         */
        for (const [key, value] of products) {
            productViews.push(<ProductView
                key={key}
                id={key}
                type={value.type}
                sku={value.sku}
                name={value.name}
                price={value.price}
                description={value.description}
                onCheckDelete={this.onCheckDelete.bind(this, key)}
                deleteDisable={deleteDisable}
                />)
        }

        return (
            <>
                <Header>
                    <Col md={3} className='float-start'>
                        <h1>Products List</h1>
                    </Col>
                    <div className='float-end'>
                        <Button variant='outline-primary' href='/add-product' className='ms-1'>ADD</Button>
                        <Button
                            className='ms-1'
                            id='delete-product-btn'
                            variant='danger'
                            onClick={this.deleteProduct}
                        >
                            {(isDeleting && (
                                <Spinner
                                    as="span"
                                    animation="border"
                                    size="sm"
                                    role="status"
                                    aria-hidden="true"
                                    className='me-1'
                                />))
                            }
                            MASS DELETE
                        </Button>
                    </div>
                </Header>
                <Container className='px-4 py-5'>
                    {
                        (loading && (
                            <Row className="justify-content-center">
                                <Spinner animation="border" />
                            </Row>
                        ))
                    }
                    {
                        (productViews.length < 1 && !loading && (
                            <Row className="justify-content-center">
                                <p className='text-muted text-center'>No products found</p>
                            </Row>
                        ))
                    }
                    <Row md={{cols: 4}} className='row-cols-3 g-4'>
                        {productViews}
                    </Row>
                </Container>
                <Footer />
            </>
        )
    }
}

export default ProductsList;