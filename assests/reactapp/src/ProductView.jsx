import React, {Component} from 'react'
import {Col, Card} from 'react-bootstrap'
import CurrencyFormat from 'react-currency-format';


/**
 * The component responsible of rendering a single product view in a product list
 */
class ProductView extends Component {

    /**
     * formats a product description into approprite text format
     * @param string type
     * @param object description
     */
    formatProductDescription(type, description) {
        const {
            size = 0,
            unit = '',
            weight = 0,
            height = 0,
            width = 0,
            length = 0
        } = description;

        switch (type) {
            case 'DVD':
                return `Size: ${size} ${unit}`;
            case 'Book':
                return `Weight: ${weight} ${unit}`;
            case 'Furniture':
                return `Dimensions: ${height}x${width}x${length}`;
            default:
                return '';
        }
    }

    render() {
        const {
            sku,
            name,
            price,
            description,
            id,
            type,
            onCheckDelete,
            deleteDisable
        } = this.props;

        return (
            <Col>
                <Card className='h-100'>
                    <div className='card-body text-center productCard'>
                        <input type='checkbox' className='delete-checkbox' value={id} onChange={onCheckDelete} disabled={deleteDisable} />
                        <p>{sku}</p>
                        <p>{name}</p>
                        <CurrencyFormat thousandSeparator={true} prefix={'$'} value={price} displayType='text'/>
                        <p>{this.formatProductDescription(type, description)}</p>
                    </div>
                </Card>
            </Col>
        )
    }
}

export default ProductView;
