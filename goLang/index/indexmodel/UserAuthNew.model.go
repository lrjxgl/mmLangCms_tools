package indexModel

import (
	"app/config"
)

type UserAuthNewModel struct {
	Id      uint   `gorm:"primaryKey";json:"id"`
	Userid      uint   `json:"userid"` 
	Truename      string   `json:"truename"` 
	User_card      string   `json:"user_card"` 
	Income      uint   `json:"income"` 
	Dateline      uint   `json:"dateline"` 
	Lasttime      uint   `json:"lasttime"` 
	Telephone      string   `json:"telephone"` 
	Is_auth      uint   `json:"is_auth"` 
	Status      uint   `json:"status"` 
	Content      string   `json:"content"` 
	Info      string   `json:"info"` 
	True_user_head      string   `json:"true_user_head"` 

}

func (UserAuthNewModel) TableName() string {
	return config.TablePre + "user_auth_new"
}

func UserAuthNewList(list []UserAuthNewModel) []UserAuthNewModel {
	slen := len(list)
	if slen == 0 {
		return list
	}

	for i := 0; i < slen; i++ {
		m := list[i]
		
		list[i] = m
	}
	return list
}