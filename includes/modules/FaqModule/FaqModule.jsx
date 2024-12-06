import React, { useState, useEffect } from 'react';
import axios from 'axios';
import './style.css';

const FaqModule = props => {
    const [categories, setCategories] = useState([]);
    const [faqs, setFaqs] = useState([]);
    const [openCategories, setOpenCategories] = useState([]);
    const [openQuestions, setOpenQuestions] = useState([]);

    useEffect(() => {
        const selectedCategories = props.categories ? props.categories.split(',') : [];
        const fetchData = async () => {
            try {
                const response = await axios.get('/wp-json/wp/v2/faq', {
                    params: {
                        faq_category: selectedCategories.join(','),
                        per_page: 100,
                        orderby: 'meta_value_num',
                        meta_key: '_faq_order',
                        order: 'ASC'
                    }
                });

                const categoriesResponse = await axios.get('/wp-json/wp/v2/faq_category', {
                    params: {
                        include: selectedCategories.join(','),
                        per_page: 100
                    }
                });

                setCategories(categoriesResponse.data);
                setFaqs(response.data);

                if (props.open_first_item === 'on' && response.data.length > 0) {
                    setOpenQuestions([response.data[0].id]);
                }
            } catch (error) {
                console.error('Erreur lors du chargement des FAQs:', error);
            }
        };

        fetchData();
    }, [props.categories]);

    const toggleCategory = (categoryId) => {
        setOpenCategories(prev => 
            prev.includes(categoryId)
                ? prev.filter(id => id !== categoryId)
                : [...prev, categoryId]
        );
    };

    const toggleQuestion = (questionId) => {
        setOpenQuestions(prev => 
            prev.includes(questionId)
                ? prev.filter(id => id !== questionId)
                : [...prev, questionId]
        );
    };

    const getFaqsByCategory = (categoryId) => {
        return faqs.filter(faq => 
            faq.faq_category.includes(parseInt(categoryId))
        );
    };

    return (
        <div className="divi-faq-container">
            {categories.map(category => (
                <div key={category.id} className="divi-faq-category">
                    {props.show_category_title === 'on' && (
                        <div 
                            className={`divi-faq-category-header ${openCategories.includes(category.id) ? 'open' : ''}`}
                            onClick={() => toggleCategory(category.id)}
                        >
                            <h3>{category.name}</h3>
                            <span className="et_pb_toggle_title"></span>
                        </div>
                    )}
                    
                    <div className={`divi-faq-category-content ${openCategories.includes(category.id) ? 'open' : ''}`}>
                        {getFaqsByCategory(category.id).map(faq => (
                            <div 
                                key={faq.id} 
                                className={`et_pb_toggle et_pb_module ${openQuestions.includes(faq.id) ? 'et_pb_toggle_open' : 'et_pb_toggle_close'}`}
                            >
                                <div 
                                    className="et_pb_toggle_title"
                                    onClick={() => toggleQuestion(faq.id)}
                                >
                                    <h5 dangerouslySetInnerHTML={{ __html: faq.title.rendered }}></h5>
                                </div>
                                {openQuestions.includes(faq.id) && (
                                    <div 
                                        className="et_pb_toggle_content clearfix"
                                        dangerouslySetInnerHTML={{ __html: faq.content.rendered }}
                                    >
                                    </div>
                                )}
                            </div>
                        ))}
                    </div>
                </div>
            ))}
        </div>
    );
};

export default FaqModule; 